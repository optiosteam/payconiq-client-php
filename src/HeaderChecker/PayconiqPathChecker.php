<?php
declare(strict_types = 1);

namespace Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidHeaderException;

/**
 * Class PayconiqPathChecker
 * @package Optios\Payconiq\HeaderChecker
 */
final class PayconiqPathChecker implements HeaderChecker
{
    private const HEADER_NAME = 'https://payconiq.com/path';

    /**
     * {@inheritdoc}
     */
    public function checkHeader($value): void
    {
        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            throw new InvalidHeaderException(
                sprintf('"%s" must be a valid url.', self::HEADER_NAME),
                self::HEADER_NAME,
                $value
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
