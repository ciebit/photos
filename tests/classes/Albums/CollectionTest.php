<?php
namespace Ciebit\PhotosTests\Albums;

use Ciebit\Photos\Collection as PhotoCollection;
use Ciebit\Photos\Albums\Collection;
use Ciebit\Photos\Albums\Album;
use Ciebit\Photos\Albums\Status;
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
            new Album(
                'Title Example 01',
                new PhotoCollection,
                Status::ACTIVE()
            ),
            new Album(
                'Title Example 02',
                new PhotoCollection,
                Status::ACTIVE()
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
