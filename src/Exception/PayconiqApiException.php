<?php
declare(strict_types=1);

namespace Optios\Payconiq\Exception;

class PayconiqApiException extends PayconiqBaseException
{
    public function __construct(
        protected ?string $payconiqMessage,
        protected ?string $payconiqCode,
        protected ?string $traceId,
        protected ?string $spanId,
        bool $isProd = true,
        $message = "",
        $code = 0,
        \Throwable $previous = null,
    ) {
        parent::__construct($isProd, $message, $code, $previous);
    }

    public function getPayconiqMessage(): ?string {
        return $this->payconiqMessage;
    }

    public function getPayconiqCode(): ?string {
        return $this->payconiqCode;
    }

    public function getTraceId(): ?string {
        return $this->traceId;
    }

    public function getSpanId(): ?string {
        return $this->spanId;
    }
}
