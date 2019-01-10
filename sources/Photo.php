<?php
namespace Ciebit\Photos;

use Ciebit\Files\Images\Image;
use Ciebit\Photos\Status;

class Photo
{
    /** @var string */
    private $albumId;

    /** @var string */
    private $id;

    /** @var Image */
    private $image;

    /** @var int */
    private $position;

    /** @var Status */
    private $status;

    /** @var int */
    private $views;


    public function __construct(Image $image, Status $status)
    {
        $this->albumId = '';
        $this->id = '';
        $this->image = $image;
        $this->position = 0;
        $this->status = $status;
        $this->views = 0;
    }

    /*
     * GETs
    */
    public function getAlbumId(): string
    {
        return $this->albumId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    /*
     * SETs
    */
    public function setAlbumId(string $id): self
    {
        $this->albumId = $id;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;
        return $this;
    }
}
