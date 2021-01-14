<?php

namespace Tests\Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\InvalidHeaderException;
use Optios\Payconiq\HeaderChecker\PayconiqIssChecker;
use PHPUnit\Framework\TestCase;

class PayconiqIssCheckerTest extends TestCase
{
    private $checker;

    protected function setUp(): void
    {
        $this->checker = new PayconiqIssChecker();

        parent::setUp();
    }

    public function testCheckHeader()
    {
        $this->checker->checkHeader('Payconiq');
        $this->expectException(InvalidHeaderException::class);
        $this->checker->checkHeader('InvalidPayconiq');
    }

    public function testSupportedHeader()
    {
        $this->assertEquals('https://payconiq.com/iss', $this->checker->supportedHeader());
    }
}
