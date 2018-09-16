<?php
namespace Ciebit\PhotosTests\Albums\Storages\Database;

use Ciebit\Files\Images\Image;
use Ciebit\Files\Storages\Database\Sql as FileSql;
use Ciebit\Photos\Albums\Collection;
use Ciebit\Photos\Albums\Album;
use Ciebit\Photos\Albums\Status;
use Ciebit\Photos\Albums\Storages\Database\Sql;
use Ciebit\Photos\Storages\Database\Sql as PhotoStorage;
use Ciebit\PhotosTests\Storages\Database\Connection;

class SqlTest extends Connection
{
    public function getDatabase(): Sql
    {
        $pdo = $this->getPdo();
        $fileStorage = new FileSql($pdo);
        $albumStorage = new PhotoStorage($pdo, $fileStorage);
        return new Sql($pdo, $albumStorage, $fileStorage);
    }

    public function testGet(): void
    {
        $database = $this->getDatabase();
        $album = $database->get();
        $this->assertInstanceOf(Album::class, $album);
    }

    public function testGetFilterById(): void
    {
        $id = 2;
        $database = $this->getDatabase();
        $database->addFilterById('=', $id + 0);
        $album = $database->get();
        $this->assertEquals($id, $album->getId());
        $this->assertInstanceOf(Image::class, $album->getCover());
    }

    public function testGetFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus('=', Status::DRAFT());
        $album = $database->get();
        $this->assertEquals(Status::DRAFT(), $album->getStatus());
    }

    public function testGetOrder(): void
    {
        $database = $this->getDatabase();
        $database->addOrderBy('id', 'DESC');
        $album = $database->get();
        $this->assertEquals(3, $album->getId());
    }

    public function testGetAll(): void
    {
        $database = $this->getDatabase();
        $collection = $database->getAll();
        $this->assertInstanceOf(Collection::class, $collection);
    }

    public function testGetAllFilterById(): void
    {
        $database = $this->getDatabase();
        $database->addFilterById('IN', 2, 3);
        $collection = $database->getAll();
        $this->assertCount(2, $collection);

        $albums = $collection->getArrayObject();
        $ids = [
            $albums->offsetGet(0)->getId(),
            $albums->offsetGet(1)->getId()
        ];
        $this->assertArraySubset([2,3], $ids);
        $this->assertInstanceOf(Image::class, $albums->offsetGet(0)->getCover());
    }

    public function testGetAllFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus('=', Status::DRAFT());
        $collection = $database->getAll();
        $this->assertCount(1, $collection);
        $this->assertEquals(Status::DRAFT(), $collection->getArrayObject()->offsetGet(0)->getStatus());
    }

    public function testGetAllLimit(): void
    {
        $database = $this->getDatabase();
        $database->setLimit(2);
        $collection = $database->getAll();
        $this->assertCount(2, $collection);
    }

    public function testGetAllOrder(): void
    {
        $database = $this->getDatabase();
        $database->addOrderBy('id', 'DESC');
        $collection = $database->getAll();
        $this->assertEquals(3, $collection->getArrayObject()->offsetGet(0)->getId());
    }
}
