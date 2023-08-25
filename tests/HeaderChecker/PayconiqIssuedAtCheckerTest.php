<?php

namespace Tests\Optios\Payconiq\HeaderChecker;

use Carbon\Carbon;
use Jose\Component\Checker\InvalidHeaderException;
use Optios\Payconiq\HeaderChecker\PayconiqIssuedAtChecker;
use PHPUnit\Framework\TestCase;

class PayconiqIssuedAtCheckerTest extends TestCase
{
    private $checker;

    protected function setUp(): void
    {
        $this->checker = new PayconiqIssuedAtChecker();

        parent::setUp();
    }

    public function testCheckHeader()
    {
        $this->checker->checkHeader('2023-08-25T08:28:21.675129286Z');
        $this->expectException(InvalidHeaderException::class);
        $this->expectExceptionMessage('"https://payconiq.com/iat" has an invalid date format');
        $this->checker->checkHeader('Invalid date');
    }

    public function testCheckHeaderWhenInFuture()
    {
        $this->expectException(InvalidHeaderException::class);
        $this->expectExceptionMessage('The JWT is issued in the future.');
        $this->checker->checkHeader(Carbon::tomorrow()->format('Y-m-d\TH:i:s.vuO'));
    }

    public function testSupportedHeader()
    {
        $this->assertEquals('https://payconiq.com/iat', $this->checker->supportedHeader());
    }

    public function testProtectedHeaderOnly()
    {
        $this->assertFalse($this->checker->protectedHeaderOnly());
    }
}
