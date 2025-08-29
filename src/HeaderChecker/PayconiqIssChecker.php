<?php

declare(strict_types=1);

namespace Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidHeaderException;

final class PayconiqIssChecker implements HeaderChecker
{
    private const HEADER_NAME = 'https://payconiq.com/iss';
    private const ISS_VALUE = 'Payconiq';

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

        if ($value !== self::ISS_VALUE) {
            throw new InvalidHeaderException(
                message: sprintf('"%s" should be "%s"', self::HEADER_NAME, self::ISS_VALUE),
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
