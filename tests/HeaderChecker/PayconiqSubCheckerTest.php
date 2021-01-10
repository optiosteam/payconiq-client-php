<?php

namespace Tests\Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\InvalidHeaderException;
use Optios\Payconiq\HeaderChecker\PayconiqSubChecker;
use PHPUnit\Framework\TestCase;

class PayconiqSubCheckerTest extends TestCase
{
    private $checker;

    protected function setUp(): void
    {
        $this->checker = new PayconiqSubChecker('abcdef');

        parent::setUp();
    }

    public function testCheckHeader()
    {
        $this->checker->checkHeader('abcdef');
        $this->expectException(InvalidHeaderException::class);
        $this->checker->checkHeader('fedcba');
    }

    public function testSupportedHeader()
    {
        $this->assertEquals('https://payconiq.com/sub', $this->checker->supportedHeader());
    }
}
