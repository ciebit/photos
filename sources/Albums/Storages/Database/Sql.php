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
use Ciebit\Photos\Storages\Storage as PhotoStorage;
use DateTime;
use Exception;
use PDO;

use function array_column;
use function array_map;
use function count;
use function implode;

class Sql extends SqlHelper implements Database
{
    private $fileStorage; #: FileStorage
    private $photoStorage; #: PhotoStorage
    private $pdo; #PDO
    private $tableGet; #string

    public function __construct(PDO $pdo, PhotoStorage $photoStorage, FileStorage $fileStorage)
    {
        parent::__construct();

        $this->fileStorage = $fileStorage;
        $this->photoStorage = $photoStorage;
        $this->pdo = $pdo;
        $this->tableGet = 'cb_photos_albums';
    }

    public function addFilterById(string $operator, string ...$ids): Storage
    {
        $this->addSqlParam('`albums`.`id`', $operator, $ids);
        return $this;
    }

    public function addFilterByStatus(string $operator, Status ...$statusList): Storage
    {
        $this->addSqlParam('`albums`.`status`', $operator, $statusList);
        return $this;
    }

    public function addOrderBy(string $column, string $order = "ASC"): Storage
    {
        $this->addSqlOrderBy($column, $order);
        return $this;
    }

    private function build(array $data): Album
    {
        $album = new Album(
            $data['title'],
            $data['photos'],
            new Status((int) $data['status'])
        );

        if (isset($data['cover']) && $data['cover'] instanceof Image) {
            $album->setCover($data['cover']);
        }

        $album
        ->setDateTime(new DateTime($data['date_time']))
        ->setDescription($data['description'])
        ->setId($data['id'])
        ->setLanguage($data['language'])
        ->setUri($data['uri'])
        ;

        return $album;
    }

    public function get(): ?Album
    {
        $columns = array_map(
            function($column) {
                return "`albums`.`{$column}`";
            },
            $this->getColumns()
        );
        $columns = implode(',', $columns);

        $statement = $this->pdo->prepare(
            "SELECT SQL_CALC_FOUND_ROWS
            {$columns}
            FROM {$this->tableGet} as `albums`
            WHERE {$this->generateSqlFilters()}
            {$this->generateSqlOrder()}
            LIMIT 1"
        );

        $this->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.photos.albums.storages.database.sql.get_error', 2);
        }

        $albumData = $statement->fetch(PDO::FETCH_ASSOC);
        if ($albumData == false) {
            return null;
        }

        $photoStorage = clone $this->photoStorage;
        $albumData['photos'] = $photoStorage->addFilterByAlbumId('=', $albumData['id'])->getAll();

        if ($albumData['cover_id'] > 0) {
            $fileStorage = clone $this->fileStorage;
            $albumData['cover'] = $fileStorage->addFilterById($albumData['cover_id'])->get();
        }

        if (! $albumData['photos'] instanceof PhotosCollection) {
            throw new Exception('ciebit.photos.albums.storages.database.sql.image_not_found', 3);
        }

        return $this->build($albumData);
    }

    public function getAll(): Collection
    {
        $columns = array_map(
            function($column) {
                return "`albums`.`{$column}`";
            },
            $this->getColumns()
        );
        $columns = implode(',', $columns);

        $statement = $this->pdo->prepare(
            "SELECT SQL_CALC_FOUND_ROWS
            {$columns}
            FROM {$this->tableGet} as `albums`
            WHERE {$this->generateSqlFilters()}
            {$this->generateSqlOrder()}
            {$this->generateSqlLimit()}"
        );

        $this->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.photos.albums.storages.database.sql.get_all_error', 4);
        }

        $collection = new Collection;
        $albumsData = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($albumsData == false) {
            return $collection;
        }

        $albumsId = array_column($albumsData, 'id');
        $photoStorage = clone $this->photoStorage;
        $photos = $photoStorage->addFilterByAlbumId('IN', ...$albumsId)->getAll();

        $coversId = array_column($albumsData, 'cover_id');
        $coversId = array_filter($coversId);
        $images = (clone $this->fileStorage)->addFilterByIds('=', ...$coversId)->getAll();

        foreach ($albumsData as $albumData) {
            $albumData['photos'] = $photos->getByAlbumId($albumData['id']);
            if ($albumData['cover_id'] > 0) {
                $albumData['cover'] = $images->getById($albumData['cover_id']);
            }
            $collection->add(
                $this->build($albumData)
            );
        }

        return $collection;
    }

    private function getColumns(): array
    {
        return [
            'cover_id',
            'date_time',
            'description',
            'id',
            'language',
            'status',
            'title',
            'uri',
        ];
    }

    public function setLimit(int $limit): Storage
    {
        parent::setSqlLimit($limit);
        return $this;
    }

    public function getTotalRecords(): int
    {
        return (int) $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
    }

    public function setOffset(int $limit): Storage
    {
        parent::setSqlOffset($limit);
        return $this;
    }

    public function setTableGet(string $name): Database
    {
        $this->tableGet = $name;
        return $this;
    }

}
