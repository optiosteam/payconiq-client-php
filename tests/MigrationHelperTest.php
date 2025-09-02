<?php

namespace Tests\Optios\Payconiq;

use Carbon\CarbonImmutable;
use Optios\Payconiq\MigrationHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MigrationHelperTest extends TestCase
{
    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
    }

    #[DataProvider('provideSwitchCases')]
    public function testSwitchToNewEndpoints(string $now, bool $expected): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse($now, MigrationHelper::TIMEZONE),
        );

        $this->assertSame($expected, MigrationHelper::switchToNewEndpoints());
    }

    public static function provideSwitchCases(): iterable
    {
        // SWITCH_DATETIME = '2025-10-19 05:50:00' in Europe/Brussels
        yield 'day before' => ['2025-10-18 12:00:00', false];
        yield 'one second before' => ['2025-10-19 05:49:59', false];
        yield 'exact switch time' => ['2025-10-19 05:50:00', true];
        yield 'one second after' => ['2025-10-19 05:50:01', true];
        yield 'day after' => ['2025-10-20 12:00:00', true];
        yield 'month after' => ['2025-11-19 05:50:00', true];
    }
}
