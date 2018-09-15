<?php
namespace Ciebit\Photos;

use Ciebit\Photos\Photo;
use ArrayIterator;
use ArrayObject;
use Countable;
use IteratorAggregate;

use function in_array;

class Collection implements Countable, IteratorAggregate
{
    private $photos; #: ArrayObject

    public function __construct()
    {
        $this->photos = new ArrayObject;
    }

    public function add(Photo ...$photos): self
    {
        foreach ($photos as $photo) {
            $this->photos->append($photo);
        }
        return $this;
    }

    public function getArrayObject(): ArrayObject
    {
        return clone $this->photos;
    }

    public function getByAlbumId(string ...$ids): Collection
    {
        $collection = new Collection;

        foreach ($this->getIterator() as $photo) {
            if (in_array($photo->getAlbumId(), $ids)) {
                $collection->add($photo);
            }
        }

        return $collection;
    }

    public function getIterator(): ArrayIterator
    {
        return $this->photos->getIterator();
    }

    public function count(): int
    {
        return $this->photos->count();
    }
}
