<?php
namespace Ciebit\Photos\Albums;

use Ciebit\Files\Images\Image;
use Ciebit\Photos\Albums\Status;
use DateTime;

class Album
{
    /** @var Image */
    private $cover;

    /** @var DateTime */
    private $dateTime;

    /** @var string */
    private $description;

    /** @var string */
    private $id;

    /** @var string */
    private $language;

    /** @var Status */
    private $status;

    /** @var string */
    private $title;

    /** @var string */
    private $uri;

    public function __construct(string $title, Status $status)
    {
        $this->dateTime = new DateTime;
        $this->description = '';
        $this->id = '';
        $this->language = 'pt-BR';
        $this->status = $status;
        $this->title = $title;
        $this->uri = '';
    }

    /*
     * GETs
    */
    public function getCover(): ?Image
    {
        return $this->cover;
    }

    public function getDateTime(): DateTime
    {
        return $this->dateTime;
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
    public function setCover(Image $image): self
    {
        $this->cover = $image;
        return $this;
    }

    public function setDateTime(DateTime $value): self
    {
        $this->dateTime = $value;
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
