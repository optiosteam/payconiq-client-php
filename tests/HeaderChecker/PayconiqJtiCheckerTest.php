<?php

namespace Tests\Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\InvalidHeaderException;
use Optios\Payconiq\HeaderChecker\PayconiqJtiChecker;
use PHPUnit\Framework\TestCase;

class PayconiqJtiCheckerTest extends TestCase
{
    private $checker;

    protected function setUp(): void
    {
        $this->checker = new PayconiqJtiChecker();

        parent::setUp();
    }

    public function testCheckHeader()
    {
        $this->checker->checkHeader('valid');
        $this->expectException(InvalidHeaderException::class);
        $this->checker->checkHeader(null);
    }

    public function testSupportedHeader()
    {
        $this->assertEquals('https://payconiq.com/jti', $this->checker->supportedHeader());
    }
}
