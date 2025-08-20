<?php

declare(strict_types=1);

namespace Optios\Payconiq\Enum;

enum QrImageSize: string
{
    case SMALL = 'S';
    case MEDIUM = 'M';
    case LARGE = 'L';
    case EXTRA_LARGE = 'XL';
}
