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
    public const IAT_FORMAT = 'Y-m-d\TH:i:s.uO';

    private const HEADER_NAME = 'https://payconiq.com/iat';

    /**
     * {@inheritdoc}
     */
    public function checkHeader($value): void
    {
        try {
            // Payconiq unexpectedly changed their format on 2023-08-23 to include nanoseconds,
            // Since PHP doesn't support nanoseconds, we're "hacking" it by trimming it to microseconds

            // It seems that on 2023-08-28 Payconiq changed it back to microsecond format, but since we can't trust
            // their api, the regex determines if it's nanosecond or microsecond format and trims when needed

            if (preg_match('/(?:\.)(\d{9})(?:Z|\+|-)/', $value)) { // format with nanoseconds
                $pos     = strpos($value, '.');
                $trimmed = substr($value, 0, $pos + 7) . substr($value, $pos + 10);

                $iat = Carbon::createFromFormat(self::IAT_FORMAT, $trimmed);
            } else {
                $iat = new Carbon($value);
            }
        } catch (\Exception $e) {
            throw new InvalidHeaderException(
                sprintf('"%s" has an invalid date format', self::HEADER_NAME),
                self::HEADER_NAME,
                $value
            );
        }

        if ($iat->setMicroseconds(0)->gt(Carbon::now('UTC'))) {
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
