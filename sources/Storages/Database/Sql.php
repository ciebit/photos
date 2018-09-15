<?php
namespace Ciebit\Photos\Storages\Database;

use Ciebit\Photos\Photo;
use Ciebit\Photos\Status;
use Ciebit\Photos\Helpers\Sql as SqlHelper;
use Ciebit\Photos\Storages\Storage;
use Ciebit\Files\Images\Image;
use Ciebit\Files\Storages\Storage as FileStorage;
use Exception;
use PDO;

use function array_map;
use function count;
use function implode;

class Sql extends SqlHelper implements Database
{
    private $fileStorage; #: FileStorage
    private $pdo; #PDO
    private $tableGet; #string

    public function __construct(PDO $pdo, FileStorage $fileStorage)
    {
        parent::__construct();

        $this->fileStorage = $fileStorage;
        $this->pdo = $pdo;
        $this->tableGet = 'cb_photos_associations';
    }

    public function addFilterByAlbumId(string $operator, string ...$ids): Storage
    {
        $this->addSqlParam('`photos`.`album_id`', $operator, $ids);
        return $this;
    }

    public function addFilterById(string $operator, string ...$ids): Storage
    {
        $this->addSqlParam('`photos`.`id`', $operator, $ids);
        return $this;
    }

    public function addFilterByStatus(string $operator, Status ...$statusList): Storage
    {
        $this->addSqlParam('`photos`.`status`', $operator, $statusList);
        return $this;
    }

    public function addOrderBy(string $column, string $order = "ASC"): Storage
    {
        $this->addSqlOrderBy($column, $order);
        return $this;
    }

    private function build(array $data): Photo
    {
        $photo = new Photo(
            $data['image'],
            new Status((int) $data['status'])
        );

        $photo
        ->setId($data['id'])
        ->setPosition((int) $data['position'])
        ->setViews((int) $data['views'])
        ;

        return $photo;
    }

    public function get(): ?Photo
    {
        $columns = array_map(
            function($column) {
                return "`photos`.`{$column}`";
            },
            $this->getColumns()
        );
        $columns = implode(',', $columns);

        $statement = $this->pdo->prepare(
            $sql  = "SELECT SQL_CALC_FOUND_ROWS
            {$columns}
            FROM {$this->tableGet} as `photos`
            WHERE {$this->generateSqlFilters()}
            {$this->generateSqlOrder()}
            LIMIT 1"
        );

        $this->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.photos.storages.database.sql.get_error', 2);
        }

        $photoData = $statement->fetch(PDO::FETCH_ASSOC);
        if ($photoData == false) {
            return null;
        }

        $fileStorage = clone $this->fileStorage;

        $photoData['image'] = $fileStorage->addFilterById($photoData['file_id'])->get();

        if (! $photoData['image'] instanceof Image) {
            throw new Exception('ciebit.photos.storages.database.sql.image_not_found', 3);
        }

        return $this->build($photoData);
    }

    private function getColumns(): array
    {
        return [
            'album_id',
            'file_id',
            'id',
            'position',
            'status',
            'views',
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
