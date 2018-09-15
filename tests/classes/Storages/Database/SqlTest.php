<?php
namespace Ciebit\PhotosTests\Storages\Database;

use Ciebit\Files\Storages\Database\Sql as FileSql;
use Ciebit\Photos\Collection;
use Ciebit\Photos\Photo;
use Ciebit\Photos\Status;
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

    public function testGetFilterByAlbumId(): void
    {
        $id = 2;
        $database = $this->getDatabase();
        $database->addFilterByAlbumId('=', $id + 0);
        $photo = $database->get();
        $this->assertEquals(4, $photo->getId());
    }

    public function testGetFilterById(): void
    {
        $id = 3;
        $database = $this->getDatabase();
        $database->addFilterById('=', $id + 0);
        $photo = $database->get();
        $this->assertEquals($id, $photo->getId());
    }

    public function testGetFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus('=', Status::DRAFT());
        $photo = $database->get();
        $this->assertEquals(Status::DRAFT(), $photo->getStatus());
    }

    public function testGetAll(): void
    {
        $database = $this->getDatabase();
        $collection = $database->getAll();
        $this->assertInstanceOf(Collection::class, $collection);
    }
}
