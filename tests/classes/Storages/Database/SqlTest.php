<?php
namespace Ciebit\PhotosTests\Storages\Database;

use Ciebit\Files\Storages\Database\Sql as FileSql;
use Ciebit\Photos\Collection;
use Ciebit\Photos\Photo;
use Ciebit\Photos\Storages\Database\Sql;
use Ciebit\PhotosTests\Storages\Database\Connection;

class SqlTest extends Connection
{
    public function getDatabase(): Sql
    {
        $pdo = $this->getPdo();
        $fileStorage = new FileSql($pdo);
        return new Sql($pdo, $fileStorage);
    }

    public function testGet(): void
    {
        $database = $this->getDatabase();
        $photo = $database->get();
        $this->assertInstanceOf(Photo::class, $photo);
    }
}
