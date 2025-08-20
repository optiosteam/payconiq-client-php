<?php

namespace Tests\Optios\Payconiq\Exception;

use Optios\Payconiq\Exception\PayconiqApiException;
use PHPUnit\Framework\TestCase;

class PayconiqApiExceptionTest extends TestCase
{
    public function testException(): void
    {
        $exception = new PayconiqApiException(
            payconiqMessage: 'a message',
            payconiqCode: 'code',
            traceId: 'trace-id',
            spanId: 'span-id',
        );

        $this->assertEquals('a message', $exception->getPayconiqMessage());
        $this->assertEquals('code', $exception->getPayconiqCode());
        $this->assertEquals('trace-id', $exception->getTraceId());
        $this->assertEquals('span-id', $exception->getSpanId());
        $this->assertTrue($exception->isUseProd());
    }
}
