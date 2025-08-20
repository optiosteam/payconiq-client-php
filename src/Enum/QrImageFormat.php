<?php

declare(strict_types=1);

namespace Optios\Payconiq\Enum;

enum QrImageFormat: string
{
    case PNG = 'PNG';
    case SVG = 'SVG';
}
