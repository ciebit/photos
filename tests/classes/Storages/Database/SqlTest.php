<?php
namespace Ciebit\PhotosTests\Storages\Database;

use Ciebit\Files\Storages\Database\Sql as FileSql;
use Ciebit\Labels\Storages\Database\Sql as LabelSql;
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
        $labelStorage = new LabelSql($pdo);
        $fileStorage = new FileSql($pdo, $labelStorage);
        return new Sql($pdo, $fileStorage);
    }

    public function testGet(): void
    {
        $database = $this->getDatabase();
        $photo = $database->findOne();
        $this->assertInstanceOf(Photo::class, $photo);
    }

    public function testGetFilterByAlbumId(): void
    {
        $id = 2;
        $database = $this->getDatabase();
        $database->addFilterByAlbumId('=', $id + 0);
        $photo = $database->findOne();
        $this->assertEquals(4, $photo->getId());
    }

    public function testGetFilterById(): void
    {
        $id = 3;
        $database = $this->getDatabase();
        $database->addFilterById('=', $id + 0);
        $photo = $database->findOne();
        $this->assertEquals($id, $photo->getId());
    }

    public function testGetFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus('=', Status::DRAFT());
        $photo = $database->findOne();
        $this->assertEquals(Status::DRAFT(), $photo->getStatus());
    }

    public function testGetOrder(): void
    {
        $database = $this->getDatabase();
        $database->addOrderBy(Sql::FIELD_VIEWS, 'DESC');
        $photo = $database->findOne();
        $this->assertEquals(5, $photo->getViews());
    }

    public function testGetAll(): void
    {
        $database = $this->getDatabase();
        $collection = $database->findAll();
        $this->assertInstanceOf(Collection::class, $collection);
    }

    public function testGetAllFilterByAlbumId(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByAlbumId('IN', 1, 2);
        $collection = $database->findAll();
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(5, $collection);
    }

    public function testGetAllFilterById(): void
    {
        $database = $this->getDatabase();
        $database->addFilterById('IN', 2, 3);
        $collection = $database->findAll();
        $this->assertCount(2, $collection);

        $photos = $collection->getArrayObject();
        $ids = [
            $photos->offsetGet(0)->getId(),
            $photos->offsetGet(1)->getId()
        ];
        $this->assertArraySubset([2,3], $ids);
    }

    public function testGetAllFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus('=', Status::DRAFT());
        $collection = $database->findAll();
        $this->assertCount(1, $collection);
        $this->assertEquals(Status::DRAFT(), $collection->getArrayObject()->offsetGet(0)->getStatus());
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
        $database->addOrderBy('views', 'DESC');
        $collection = $database->findAll();
        $this->assertEquals(5, $collection->getArrayObject()->offsetGet(0)->getViews());
    }
}
