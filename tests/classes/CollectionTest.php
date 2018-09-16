<?php
namespace Ciebit\PhotosTests;

use Ciebit\Files\Images\Image;
use Ciebit\Photos\Photo;
use Ciebit\Photos\Collection;
use Ciebit\Photos\Status;
use Ciebit\PhotosTests\Builders\Image as ImageBuilder;
use Countable;
use IteratorAggregate;
use PHPUnit\Framework\TestCase;
use TypeError;

class CollectionTest extends TestCase
{
    public function testBasic(): void
    {
        $collection =  new Collection;

        $collection->add(
            new Photo(
                ImageBuilder::build(),
                Status::ACTIVE()
            ),
            new Photo(
                ImageBuilder::build(),
                Status::DRAFT()
            )
        );

        $this->assertInstanceOf(Countable::class, $collection);
        $this->assertInstanceOf(IteratorAggregate::class, $collection);
        $this->assertCount(2, $collection);
    }

    public function testGetByAlbumId(): void
    {
        $collection =  new Collection;
        $photo01 = new Photo(
            ImageBuilder::build(),
            Status::ACTIVE()
        );
        $photo02 = clone $photo01;

        $photo01->setAlbumId(1);
        $photo02->setAlbumId(2);

        $collection->add($photo01, $photo02);
        $this->assertEquals(2, $collection->getByAlbumId(2)->getArrayObject()->offsetGet(0)->getAlbumId());
    }

    public function testGetById(): void
    {
        $collection =  new Collection;
        $photo01 = new Photo(
            ImageBuilder::build(),
            Status::ACTIVE()
        );
        $photo02 = clone $photo01;

        $photo01->setId(1);
        $photo02->setId(2);

        $collection->add($photo01, $photo02);
        $this->assertEquals(2, $collection->getById(2)->getId());
    }

    public function testInvalidValue(): void
    {
        $this->expectException(TypeError::class);

        $collection = new Collection;
        $collection->add('test');
    }

    public function testExternalInsertion(): void
    {
        $collection = new Collection;
        $collection->getArrayObject()->append('test');
        $this->assertCount(0, $collection);
    }
}
