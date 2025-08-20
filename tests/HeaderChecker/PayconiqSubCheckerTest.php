<?php

namespace Tests\Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\InvalidHeaderException;
use Optios\Payconiq\HeaderChecker\PayconiqSubChecker;
use PHPUnit\Framework\TestCase;

class PayconiqSubCheckerTest extends TestCase
{
    private $checker;

    protected function setUp(): void {
        parent::setUp();

        $this->checker = new PayconiqSubChecker('abcdef');
    }

    public function testCheckHeader() {
        $this->checker->checkHeader('abcdef');
        $this->expectException(InvalidHeaderException::class);
        $this->expectExceptionMessage('"https://payconiq.com/sub" should match the Payment profile ID');
        $this->checker->checkHeader('fedcba');
    }

    public function testCheckHeaderWhenNotString() {
        $this->expectException(InvalidHeaderException::class);
        $this->expectExceptionMessage('"https://payconiq.com/sub" must be a string.');
        $this->checker->checkHeader([]);
    }

    public function testSupportedHeader() {
        $this->assertEquals('https://payconiq.com/sub', $this->checker->supportedHeader());
    }

    public function testProtectedHeaderOnly() {
        $this->assertFalse($this->checker->protectedHeaderOnly());
    }
}
