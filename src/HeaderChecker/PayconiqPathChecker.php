<?php

declare(strict_types=1);

namespace Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidHeaderException;

final class PayconiqPathChecker implements HeaderChecker
{
    private const HEADER_NAME = 'https://payconiq.com/path';

    /**
     * {@inheritdoc}
     * @throws InvalidHeaderException
     */
    public function checkHeader($value): void
    {
        if (false === filter_var($value, FILTER_VALIDATE_URL)) {
            throw new InvalidHeaderException(
                message: sprintf('"%s" must be a valid url.', self::HEADER_NAME),
                header: self::HEADER_NAME,
                value: $value,
            );
        }
    }

    public function supportedHeader(): string
    {
        return self::HEADER_NAME;
    }

    public function protectedHeaderOnly(): bool
    {
        return false;
    }
}
