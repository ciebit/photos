<?php
namespace Ciebit\Photos\Albums\Storages\Database;

use Ciebit\Files\Images\Image;
use Ciebit\Files\Storages\Storage as FileStorage;
use Ciebit\Photos\Albums\Album;
use Ciebit\Photos\Albums\Collection;
use Ciebit\Photos\Albums\Status;
use Ciebit\Photos\Albums\Storages\Storage;
use Ciebit\Photos\Collection as PhotosCollection;
use Ciebit\Photos\Helpers\Sql as SqlHelper;
use DateTime;
use Exception;
use PDO;

use function array_column;
use function array_map;
use function count;
use function implode;

class Sql implements Database
{
    /** @var string */
    public const FIELD_COVER_ID = 'cover_id';

    /** @var string */
    public const FIELD_DATETIME = 'date_time';

    /** @var string */
    public const FIELD_DESCRIPTION = 'description';

    /** @var string */
    public const FIELD_ID = 'id';

    /** @var string */
    public const FIELD_LANGUAGE = 'language';

    /** @var string */
    public const FIELD_STATUS = 'status';

    /** @var string */
    public const FIELD_TITLE = 'title';

    /** @var string */
    public const FIELD_URI = 'uri';

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
        $this->sqlHelper = new SqlHelper;
        $this->pdo = $pdo;
        $this->table = 'cb_photos_albums';
        $this->totalItemsLastQuery = 0;
    }

    private function addFilter(string $fieldName, int $type, string $operator, ...$value): self
    {
        $field = "`{$this->table}`.`{$fieldName}`";
        $this->sqlHelper->addFilterBy($field, $type, $operator, ...$value);
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

    public function addFilterByUri(string $operator, string ...$uri): Storage
    {
        $this->addFilter(self::FIELD_URI, PDO::PARAM_STR, $operator, ...$uri);
        return $this;
    }

    public function addOrderBy(string $column, string $order = "ASC"): Storage
    {
        $this->sqlHelper->addOrderBy("`{$this->table}`.`{$column}`", $order);
        return $this;
    }

    private function create(array $data): Album
    {
        $album = new Album(
            $data['title'],
            new Status((int) $data['status'])
        );

        if (isset($data['cover']) && $data['cover'] instanceof Image) {
            $album->setCover($data['cover']);
        }

        $album
        ->setDateTime(new DateTime($data['date_time']))
        ->setId($data['id'])
        ->setUri($data['uri'])
        ;

        $data['description'] != null && $album->setDescription($data['description']);
        $data['language'] != null && $album->setLanguage($data['language']);

        return $album;
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
            throw new Exception('ciebit.photos.albums.storages.database.sql.get_all_error', 4);
        }

        $collection = new Collection;
        $albumsData = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($albumsData == false) {
            return $collection;
        }

        $this->totalItemsLastQuery = (int) $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();

        $coversId = array_column($albumsData, self::FIELD_COVER_ID);
        $coversId = array_filter($coversId);
        $images = (clone $this->fileStorage)->addFilterByIds('=', ...$coversId)->getAll();

        foreach ($albumsData as $albumData) {
            if ($albumData['cover_id'] > 0) {
                $albumData['cover'] = $images->getById($albumData['cover_id']);
            }
            $collection->add(
                $this->create($albumData)
            );
        }

        return $collection;
    }

    /** @throws Exception */
    public function findOne(): ?Album
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
            throw new Exception('ciebit.photos.albums.storages.database.sql.get_error', 2);
        }

        $albumData = $statement->fetch(PDO::FETCH_ASSOC);
        if ($albumData == false) {
            return null;
        }

        if ($albumData['cover_id'] > 0) {
            $fileStorage = clone $this->fileStorage;
            $albumData['cover'] = $fileStorage->addFilterById($albumData['cover_id'])->get();
        }

        return $this->create($albumData);
    }

    private function getFields(): string
    {
        $table = $this->table;
        $fields = [
            self::FIELD_COVER_ID,
            self::FIELD_DATETIME,
            self::FIELD_DESCRIPTION,
            self::FIELD_ID,
            self::FIELD_LANGUAGE,
            self::FIELD_STATUS,
            self::FIELD_TITLE,
            self::FIELD_URI
        ];

        $fields = array_map(
            function($field) use ($table){
                return "`{$table}`.`{$field}`";
            },
            $fields
        );

        return implode(',', $fields);
    }

    public function getTotalRecords(): int
    {
        return $this->totalItemsLastQuery;
    }

    public function setLimit(int $limit): Storage
    {
        $this->sqlHelper->setLimit($limit);
        return $this;
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
