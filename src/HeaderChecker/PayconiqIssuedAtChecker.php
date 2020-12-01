<?php
declare(strict_types = 1);

namespace Optios\Payconiq\HeaderChecker;

use Carbon\Carbon;
use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidHeaderException;

/**
 * Class PayconiqIssuedAtChecker
 * @package Optios\Payconiq\HeaderChecker
 */
final class PayconiqIssuedAtChecker implements HeaderChecker
{
    private const HEADER_NAME = 'https://payconiq.com/iat';

    /**
     * {@inheritdoc}
     */
    public function checkHeader($value): void
    {
        try {
            $iat = new Carbon($value);

            if ($iat->gt(Carbon::now('UTC'))) {
                throw new InvalidHeaderException(
                    'The JWT is issued in the future.',
                    self::HEADER_NAME,
                    $value
                );
            }
        } catch (\Exception $e) {
            throw new InvalidHeaderException(
                sprintf('"%s" has an invalid date format', self::HEADER_NAME),
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
