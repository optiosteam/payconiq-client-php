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
            // Payconiq unexpectedly changed their format on 2023-08-23 to include nanoseconds,
            // Since PHP doesn't support nanoseconds, we're "hacking" it by trimming it to microseconds
            $pos     = strpos($value, '.');
            $trimmed = substr($value, 0, $pos + 7) . substr($value, $pos + 10);

            $iat = Carbon::createFromFormat('Y-m-d\TH:i:s.uO', $trimmed);
        } catch (\Exception $e) {
            throw new InvalidHeaderException(
                sprintf('"%s" has an invalid date format', self::HEADER_NAME),
                self::HEADER_NAME,
                $value
            );
        }

        if ($iat->gt(Carbon::now('UTC'))) {
            throw new InvalidHeaderException(
                'The JWT is issued in the future.',
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
