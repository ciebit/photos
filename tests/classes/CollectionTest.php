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
