<?php
namespace Ciebit\PhotosTests;

use Ciebit\Photos\Albums\Album;
use Ciebit\Photos\Albums\Status;
use Ciebit\Photos\Collection as PhotoCollection;
use Ciebit\PhotosTests\Builders\Image as ImageBuilder;
use DateTime;
use PHPUnit\Framework\TestCase;

class AlbumTest extends TestCase
{
    private const DATE_TIME = '2018-09-15 10:25:23';
    private const DESCRIPTION = 'Description Example';
    private const ID = '1';
    private const LANGUAGE = 'pt-BR';
    private const STATUS = 3;
    private const TITLE = 'Title Example';
    private const URI = 'uri-example';

    public function testCreateFromManual(): void
    {
        $photoCollection = new PhotoCollection;
        $album = new Album(
            self::TITLE,
            $photoCollection,
            new Status(self::STATUS)
        );
        $album
        ->setDateTime(new DateTime(self::DATE_TIME))
        ->setDescription(self::DESCRIPTION)
        ->setId(self::ID)
        ->setLanguage(self::LANGUAGE)
        ->setUri(self::URI)
        ;


        $this->assertInstanceOf(PhotoCollection::class, $album->getPhotos());
        $this->assertEquals(new DateTime(self::DATE_TIME), $album->getDateTime());
        $this->assertEquals(self::DESCRIPTION, $album->getDescription());
        $this->assertEquals(self::ID, $album->getId());
        $this->assertEquals(self::LANGUAGE, $album->getLanguage());
        $this->assertEquals(self::STATUS, $album->getStatus()->getValue());
        $this->assertEquals(self::TITLE, $album->getTitle());
        $this->assertEquals(self::URI, $album->getUri());
    }
}
