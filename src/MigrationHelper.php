<?php

declare(strict_types=1);

namespace Optios\Payconiq;

use Carbon\CarbonImmutable;

class MigrationHelper
{
    /**
     * > Belangrijk: eerder gaven we aan dat deze aanpassing op 21/09/2025 moet uitgevoerd worden.
     * > Om u en ons de nodige ruimte te geven voor extra testen en om zeker te zijn dat alles vlekkeloos verloopt,
     * > verplaatsen we de datum. De nieuwe beoogde datum waarop u de aanpassing moet uitvoeren
     * > is 19/10/2025, tussen 02:00 en 06:00 uur 's ochtends.
     */

    public const SWITCH_DATETIME = '2025-10-19 05:50:00';
    public const TIMEZONE = 'Europe/Brussels'; // CET

    public static function switchToNewEndpoints(): bool
    {
        $now = CarbonImmutable::now(self::TIMEZONE);
        $switch = CarbonImmutable::parse(self::SWITCH_DATETIME, self::TIMEZONE);

        return $now->gte($switch);
    }
}
