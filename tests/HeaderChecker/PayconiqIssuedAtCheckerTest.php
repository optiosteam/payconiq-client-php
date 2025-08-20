<?php

namespace Tests\Optios\Payconiq\HeaderChecker;

use Carbon\Carbon;
use Jose\Component\Checker\InvalidHeaderException;
use Optios\Payconiq\HeaderChecker\PayconiqIssuedAtChecker;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

class PayconiqIssuedAtCheckerTest extends TestCase
{
    private $checker;

    protected function setUp(): void {
        parent::setUp();

        $this->checker = new PayconiqIssuedAtChecker();
    }

    #[DoesNotPerformAssertions]
    public function testCheckHeader() {
        $this->checker->checkHeader('2023-08-25T08:28:21.675129Z');
    }

    #[DoesNotPerformAssertions]
    public function testCheckHeaderNanoSeconds() {
        $this->checker->checkHeader('2023-08-25T08:28:21.675129286Z');
    }

    #[DoesNotPerformAssertions]
    public function testCheckHeaderNanoSecondsOtherTimeZone() {
        $this->checker->checkHeader('2023-08-25T08:28:21.675129286+02:00');
    }

    public function testCheckHeaderException() {
        $this->expectException(InvalidHeaderException::class);
        $this->expectExceptionMessage('"https://payconiq.com/iat" has an invalid date format');
        $this->checker->checkHeader('Invalid date');
    }

    public function testCheckHeaderWhenInFuture() {
        $this->expectException(InvalidHeaderException::class);
        $this->expectExceptionMessage('The JWT is issued in the future.');
        $this->checker->checkHeader(Carbon::tomorrow()->format(PayconiqIssuedAtChecker::IAT_FORMAT));
    }

    public function testCheckHeaderWhenInFutureOtherFormat() {
        $this->expectException(InvalidHeaderException::class);
        $this->expectExceptionMessage('The JWT is issued in the future.');
        $this->checker->checkHeader('3023-08-25T08:28:21.675129286Z');
    }

    public function testSupportedHeader() {
        $this->assertEquals('https://payconiq.com/iat', $this->checker->supportedHeader());
    }

    public function testProtectedHeaderOnly() {
        $this->assertFalse($this->checker->protectedHeaderOnly());
    }
}
