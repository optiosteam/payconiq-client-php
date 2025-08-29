<?php

declare(strict_types=1);

namespace Optios\Payconiq\Enum;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case IDENTIFIED = 'IDENTIFIED';
    case AUTHORIZED = 'AUTHORIZED';
    case AUTHORIZATION_FAILED = 'AUTHORIZATION_FAILED';
    case SUCCEEDED = 'SUCCEEDED';
    case FAILED = 'FAILED';
    case CANCELLED = 'CANCELLED';
    case EXPIRED = 'EXPIRED';
}
