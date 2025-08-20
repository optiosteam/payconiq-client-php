<?php
declare(strict_types=1);

namespace Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidHeaderException;

final class PayconiqJtiChecker implements HeaderChecker
{
    private const HEADER_NAME = 'https://payconiq.com/jti';

    /**
     * {@inheritdoc}
     * @throws InvalidHeaderException
     */
    public function checkHeader($value): void
    {
        if (false === is_string($value)) {
            throw new InvalidHeaderException(
                message: sprintf('"%s" must be a string.', self::HEADER_NAME),
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
