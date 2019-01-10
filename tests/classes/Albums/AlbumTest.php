<?php
namespace Ciebit\PhotosTests;

use Ciebit\Files\Images\Image;
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
        $album = new Album(
            self::TITLE,
            new Status(self::STATUS)
        );
        $album
        ->setCover(ImageBuilder::build())
        ->setDateTime(new DateTime(self::DATE_TIME))
        ->setDescription(self::DESCRIPTION)
        ->setId(self::ID)
        ->setLanguage(self::LANGUAGE)
        ->setUri(self::URI)
        ;

        $this->assertInstanceOf(Image::class, $album->getCover());
        $this->assertEquals(new DateTime(self::DATE_TIME), $album->getDateTime());
        $this->assertEquals(self::DESCRIPTION, $album->getDescription());
        $this->assertEquals(self::ID, $album->getId());
        $this->assertEquals(self::LANGUAGE, $album->getLanguage());
        $this->assertEquals(self::STATUS, $album->getStatus()->getValue());
        $this->assertEquals(self::TITLE, $album->getTitle());
        $this->assertEquals(self::URI, $album->getUri());
    }
}
