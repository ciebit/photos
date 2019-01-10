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
        return new Sql($pdo, $fileStorage);
    }

    public function testGet(): void
    {
        $database = $this->getDatabase();
        $album = $database->findOne();
        $this->assertInstanceOf(Album::class, $album);
    }

    public function testGetFilterById(): void
    {
        $id = 2;
        $database = $this->getDatabase();
        $database->addFilterById('=', $id + 0);
        $album = $database->findOne();
        $this->assertEquals($id, $album->getId());
        $this->assertInstanceOf(Image::class, $album->getCover());
    }

    public function testGetFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus('=', Status::DRAFT());
        $album = $database->findOne();
        $this->assertEquals(Status::DRAFT(), $album->getStatus());
    }

    public function testGetFilterByUri(): void
    {
        $uri = 'uri-example-02';
        $database = $this->getDatabase();
        $database->addFilterByUri('=', $uri.'');
        $album = $database->findOne();
        $this->assertEquals($uri, $album->getUri());
    }

    public function testGetOrder(): void
    {
        $database = $this->getDatabase();
        $database->addOrderBy('id', 'DESC');
        $album = $database->findOne();
        $this->assertEquals(4, $album->getId());
    }

    public function testGetAll(): void
    {
        $database = $this->getDatabase();
        $collection = $database->findAll();
        $this->assertInstanceOf(Collection::class, $collection);
    }

    public function testGetAllFilterById(): void
    {
        $database = $this->getDatabase();
        $database->addFilterById('IN', 2, 3);
        $collection = $database->findAll();
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
        $collection = $database->findAll();
        $this->assertCount(1, $collection);
        $this->assertEquals(Status::DRAFT(), $collection->getArrayObject()->offsetGet(0)->getStatus());
    }

    public function testGetAllFilterByUri(): void
    {
        $uri = 'uri-example-03';
        $database = $this->getDatabase();
        $database->addFilterByUri('=', $uri.'');
        $albums = $database->findAll();
        $this->assertCount(1, $albums);
        $this->assertEquals($uri, $albums->getArrayObject()->offsetGet(0)->getUri());
    }

    public function testGetAllLimit(): void
    {
        $database = $this->getDatabase();
        $database->setLimit(2);
        $collection = $database->findAll();
        $this->assertCount(2, $collection);
    }

    public function testGetAllOrder(): void
    {
        $database = $this->getDatabase();
        $database->addOrderBy('id', 'DESC');
        $collection = $database->findAll();
        $this->assertEquals(4, $collection->getArrayObject()->offsetGet(0)->getId());
    }
}
