<?php
namespace Ciebit\Photos;

use MyCLabs\Enum\Enum;

class Status extends Enum
{
    const ACTIVE = 3;
    const ANALYZE = 2;
    const DRAFT = 1;
}
