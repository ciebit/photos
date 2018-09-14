<?php
namespace Ciebit\Photos\Albums;

use Ciebit\Photos\Albums\Album;
use ArrayIterator;
use ArrayObject;
use Countable;
use IteratorAggregate;

class Collection implements Countable, IteratorAggregate
{
    private $albums; #: ArrayObject

    public function __construct()
    {
        $this->albums = new ArrayObject;
    }

    public function add(Photo ...$albums): self
    {
        foreach ($albums as $album) {
            $this->albums->append($album);
        }

        return $this;
    }

    public function getArrayObject(): ArrayObject
    {
        return clone $this->albums;
    }

    public function getIterator(): ArrayIterator
    {
        return $this->albums->getIterator();
    }

    public function count(): int
    {
        return $this->albums->count();
    }
}
