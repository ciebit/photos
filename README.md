# Photos

Classes de representação de foto e álbum.


## Exemplo de Uso

```php
use Ciebit\Files\Images\Image;
use Ciebit\Files\Status as ImageStatus;
use Ciebit\Photos\Photo;
use Ciebit\Photos\Status;

$image = new Image(
    'Name Image',
    'image/jpeg',
    'uri-image.jpg',
    400, # width
    300, # height
    ImageStatus::ACTIVE()
);

$status = Status::ACTIVE();

$photo = new Photo($image, $status);
$photo
->setId('2')
->setViews(5)
->setPosition(1)
;
```
