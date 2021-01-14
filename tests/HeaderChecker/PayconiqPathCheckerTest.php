<?php

namespace Tests\Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\InvalidHeaderException;
use Optios\Payconiq\HeaderChecker\PayconiqPathChecker;
use PHPUnit\Framework\TestCase;

class PayconiqPathCheckerTest extends TestCase
{
    private $checker;

    protected function setUp(): void
    {
        $this->checker = new PayconiqPathChecker();

        parent::setUp();
    }

    public function testCheckHeader()
    {
        $this->checker->checkHeader('http://url.be');
        $this->checker->checkHeader('https://url.be');
        $this->expectException(InvalidHeaderException::class);
        $this->checker->checkHeader('invalid-url');
    }

    public function testSupportedHeader()
    {
        $this->assertEquals('https://payconiq.com/path', $this->checker->supportedHeader());
    }
}
