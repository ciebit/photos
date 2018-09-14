<?php
namespace Ciebit\Photos\Albums;

use Ciebit\Photos\Collection as PhotosCollection;
use Ciebit\Photos\Albums\Status;
use DateTime;

class Album
{
    private $dateHour; #: DateTime
    private $description; #: string
    private $id; #: string
    private $language; #: string
    private $photos; #: PhotosCollection
    private $status; #: Status
    private $title; #: string
    private $uri; #: string

    public function __construct(string $title, PhotosCollection $photos, Status $status)
    {
        $this->dateHour = new DateTime;
        $this->description = '';
        $this->id = '';
        $this->language = 'pt-BR';
        $this->photos = $photos;
        $this->status = $status;
        $this->title = $title;
        $this->uri = '';
    }

    /*
     * GETs
    */
    public function getDateHour(): DateTime
    {
        return $this->dateHour;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getPhotos(): PhotosCollection
    {
        return $this->photos;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUri(): string
    {
        return $this->uri;
    }


    /*
     * SETs
    */
    public function setDateHour(DateTime $value): self
    {
        $this->dateHour = $value;
        return $this;
    }

    public function setDescription(string $value): self
    {
        $this->description = $value;
        return $this;
    }

    public function setId(string $value): self
    {
        $this->id = $value;
        return $this;
    }

    public function setLanguage(string $value): self
    {
        $this->language = $value;
        return $this;
    }

    public function setUri(string $value): self
    {
        $this->uri = $value;
        return $this;
    }
}
