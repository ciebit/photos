<?php
namespace Ciebit\Photos\Storages\Database;

use Ciebit\Photos\Collection;
use Ciebit\Photos\Photo;
use Ciebit\Photos\Status;
use Ciebit\Photos\Helpers\Sql as SqlHelper;
use Ciebit\Photos\Storages\Storage;
use Ciebit\Files\Images\Image;
use Ciebit\Files\Storages\Storage as FileStorage;
use Exception;
use PDO;

use function array_column;
use function array_map;
use function count;
use function implode;

class Sql implements Database
{
    /** @var string */
    public const FIELD_ALBUM_ID = 'album_id';

    /** @var string */
    public const FIELD_FILE_ID = 'file_id';

    /** @var string */
    public const FIELD_ID = 'id';

    /** @var string */
    public const FIELD_POSITION = 'position';

    /** @var string */
    public const FIELD_STATUS = 'status';

    /** @var string */
    public const FIELD_VIEWS = 'views';

    /** @var FileStorage */
    private $fileStorage;

    /** @var PDO */
    private $pdo;

    /** @var SqlHelper */
    private $sqlHelper;

    /** @var string */
    private $table;

    /** @var int */
    private $totalItemsLastQuery;

    public function __construct(PDO $pdo, FileStorage $fileStorage)
    {
        $this->fileStorage = $fileStorage;
        $this->pdo = $pdo;
        $this->sqlHelper = new SqlHelper;
        $this->table = 'cb_photos_associations';
        $this->totalItemsLastQuery = 0;
    }

    public function __clone()
    {
        $this->sqlHelper = clone $this->sqlHelper;
    }

    private function addFilter(string $fieldName, int $type, string $operator, ...$value): self
    {
        $field = "`{$this->table}`.`{$fieldName}`";
        $this->sqlHelper->addFilterBy($field, $type, $operator, ...$value);
        return $this;
    }

    public function addFilterByAlbumId(string $operator, string ...$ids): Storage
    {
        $this->addFilter(self::FIELD_ALBUM_ID, PDO::PARAM_STR, $operator, ...$ids);
        return $this;
    }

    public function addFilterById(string $operator, string ...$ids): Storage
    {
        $this->addFilter(self::FIELD_ID, PDO::PARAM_STR, $operator, ...$ids);
        return $this;
    }

    public function addFilterByStatus(string $operator, Status ...$status): Storage
    {
        $this->addFilter(self::FIELD_STATUS, PDO::PARAM_STR, $operator, ...$status);
        return $this;
    }

    public function addOrderBy(string $column, string $order = "ASC"): Storage
    {
        $this->sqlHelper->addOrderBy($column, $order);
        return $this;
    }

    private function create(array $data): Photo
    {
        $photo = new Photo(
            $data['image'],
            new Status((int) $data['status'])
        );

        $photo
        ->setAlbumId($data['album_id'])
        ->setId($data['id'])
        ->setPosition((int) $data['position'])
        ->setViews((int) $data['views'])
        ;

        return $photo;
    }

    private function getFields(): string
    {
        $table = $this->table;
        $fields = [
            self::FIELD_ALBUM_ID,
            self::FIELD_FILE_ID,
            self::FIELD_ID,
            self::FIELD_POSITION,
            self::FIELD_STATUS,
            self::FIELD_VIEWS
        ];

        $fields = array_map(
            function($field) use ($table){
                return "`{$table}`.`{$field}`";
            },
            $fields
        );

        return implode(',', $fields);
    }

    /** @throws Exception */
    public function findAll(): Collection
    {
        $statement = $this->pdo->prepare(
            "SELECT SQL_CALC_FOUND_ROWS
            {$this->getFields()}
            FROM {$this->table}
            WHERE {$this->sqlHelper->generateSqlFilters()}
            {$this->sqlHelper->generateSqlOrder()}
            {$this->sqlHelper->generateSqlLimit()}"
        );

        $this->sqlHelper->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.photos.storages.database.sql.get_all_error', 4);
        }

        $collection = new Collection;
        $photosData = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($photosData == false) {
            return $collection;
        }

        $this->totalItemsLastQuery = (int) $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();

        $fileStorage = clone $this->fileStorage;
        $imagesId = array_column($photosData, 'file_id');
        $imagesId = array_map('intval', $imagesId);
        $images = $fileStorage->addFilterById('=', ...$imagesId)->findAll();

        foreach ($photosData as $photoData) {
            $photoData['image'] = $images->getById($photoData[self::FIELD_FILE_ID]);

            if (! $photoData['image'] instanceof Image) {
                throw new Exception('ciebit.photos.storages.database.sql.image_not_found', 3);
            }

            $collection->add(
                $this->create($photoData)
            );
        }

        return $collection;
    }

    /** @throws Exception */
    public function findOne(): ?Photo
    {
        $statement = $this->pdo->prepare(
            "SELECT SQL_CALC_FOUND_ROWS
            {$this->getFields()}
            FROM {$this->table}
            WHERE {$this->sqlHelper->generateSqlFilters()}
            {$this->sqlHelper->generateSqlOrder()}
            LIMIT 1"
        );

        $this->sqlHelper->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.photos.storages.database.sql.get_error', 2);
        }

        $photoData = $statement->fetch(PDO::FETCH_ASSOC);
        if ($photoData == false) {
            return null;
        }

        $this->totalItemsLastQuery = (int) $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();

        $fileStorage = clone $this->fileStorage;

        $photoData['image'] = $fileStorage->addFilterById('=', $photoData[self::FIELD_FILE_ID])->findOne();

        if (! $photoData['image'] instanceof Image) {
            throw new Exception('ciebit.photos.storages.database.sql.image_not_found', 3);
        }

        return $this->create($photoData);
    }

    public function setLimit(int $limit): Storage
    {
        $this->sqlHelper->setLimit($limit);
        return $this;
    }

    public function getTotalRecords(): int
    {
        return $this->totalItemsLastQuery;
    }

    public function setOffset(int $limit): Storage
    {
        $this->sqlHelper->setOffset($limit);
        return $this;
    }

    public function setTable(string $name): Database
    {
        $this->table = $name;
        return $this;
    }

}
