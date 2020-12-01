<?php
declare(strict_types = 1);

namespace Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidHeaderException;

/**
 * Class PayconiqIssChecker
 * @package Optios\Payconiq\HeaderChecker
 */
final class PayconiqIssChecker implements HeaderChecker
{
    private const HEADER_NAME = 'https://payconiq.com/iss';

    private const ISS_VALUE = 'Payconiq';

    /**
     * {@inheritdoc}
     */
    public function checkHeader($value): void
    {
        if (! is_string($value)) {
            throw new InvalidHeaderException(
                sprintf('"%s" must be a string.', self::HEADER_NAME),
                self::HEADER_NAME,
                $value
            );
        }

        if ($value !== self::ISS_VALUE) {
            throw new InvalidHeaderException(
                sprintf('"%s" should be "%s"', self::HEADER_NAME, self::ISS_VALUE),
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
