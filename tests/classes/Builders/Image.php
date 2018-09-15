<?php
namespace Ciebit\PhotosTests\Builders;

use Ciebit\Files\Images\Image as ImageFile;
use Ciebit\Files\Status;

class Image
{
    public const NAME = 'Image Test';
    public const MIMETYPE = 'image/jpeg';
    public const URI = 'image.jpg';
    public const WIDTH = 400;
    public const HEIGHT = 300;
    public const STATUS = 3;

    static public function build(): ImageFile
    {
        return new ImageFile(
            self::NAME,
            self::MIMETYPE,
            self::URI,
            self::WIDTH,
            self::HEIGHT,
            new Status(self::STATUS)
        );
    }
}
