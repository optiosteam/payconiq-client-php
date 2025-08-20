<?php

declare(strict_types=1);

namespace Optios\Payconiq;

use Carbon\CarbonImmutable;

class MigrationHelper
{
    /**
     * From https://docs.payconiq.be:
     *  > Your endpoints will have to be adapted on 21/09/2025 between 02:00 CET and 06:00 CET.
     *  > Any update before or after this deadline will result in your integration stopping.
     */

    public const SWITCH_DATETIME = '2025-09-21 03:00:00'; // switch to new endpoints at 3AM CET (1 hour buffer)
    public const TIMEZONE = 'Europe/Brussels'; // CET

    public static function switchToNewEndpoints(): bool {
        $now = CarbonImmutable::now(self::TIMEZONE);
        $switch = CarbonImmutable::parse(self::SWITCH_DATETIME, self::TIMEZONE);

        return $now->gte($switch);
    }
}
