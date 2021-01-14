<?php

namespace Tests\Optios\Payconiq\HeaderChecker;

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
        $this->checker->checkHeader('2020-02-01 00:00:00');
        $this->expectException(InvalidHeaderException::class);
        $this->checker->checkHeader('Invlaid date');
    }

    public function testSupportedHeader()
    {
        $this->assertEquals('https://payconiq.com/iat', $this->checker->supportedHeader());
    }
}
