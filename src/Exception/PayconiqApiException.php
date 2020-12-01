<?php
declare(strict_types=1);

namespace Optios\Payconiq\Exception;

class PayconiqApiException extends PayconiqBaseException
{
    /**
     * @var string|null
     */
    protected $payconiqMessage;

    /**
     * @var string|null
     */
    protected $payconiqCode;

    /**
     * @var string|null
     */
    protected $traceId;

    /**
     * @var string|null
     */
    protected $spanId;

    public function __construct(
        ?string $payconiqMessage,
        ?string $payconiqCode,
        ?string $traceId,
        ?string $spanId,
        bool $isProd = true,
        $message = "",
        $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($isProd, $message, $code, $previous);

        $this->payconiqMessage = $payconiqMessage;
        $this->payconiqCode = $payconiqCode;
        $this->traceId      = $traceId;
        $this->spanId       = $spanId;
    }

    /**
     * @return string|null
     */
    public function getPayconiqMessage(): ?string
    {
        return $this->payconiqMessage;
    }

    /**
     * @return string|null
     */
    public function getPayconiqCode(): ?string
    {
        return $this->payconiqCode;
    }

    /**
     * @return string|null
     */
    public function getTraceId(): ?string
    {
        return $this->traceId;
    }

    /**
     * @return string|null
     */
    public function getSpanId(): ?string
    {
        return $this->spanId;
    }
}
