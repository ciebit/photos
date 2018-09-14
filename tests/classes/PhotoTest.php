<?php
namespace Ciebit\PhotosTests;

use Ciebit\Files\Images\Image;
use Ciebit\Photos\Photo;
use Ciebit\Photos\Status;
use Ciebit\PhotosTests\Builders\Image as ImageBuilder;
use PHPUnit\Framework\TestCase;

class PhotoTest extends TestCase
{
    private const ID = 1;
    private const POSITION = 4;
    private const STATUS = 3;
    private const VIEWS = 2;

    public function testCreateFromManual(): void
    {
        $photo = new Photo(
            ImageBuilder::build(),
            new Status(self::STATUS)
        );
        $photo
        ->setId(self::ID)
        ->setPosition(self::POSITION)
        ->setViews(self::VIEWS)
        ;

        $this->assertInstanceOf(Image::class, $photo->getImage());
        $this->assertEquals(self::ID, $photo->getId());
        $this->assertEquals(self::POSITION, $photo->getPosition());
        $this->assertEquals(self::STATUS, $photo->getStatus()->getValue());
        $this->assertEquals(self::VIEWS, $photo->getViews());
    }
}
