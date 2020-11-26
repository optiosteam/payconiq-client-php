<?php


class PayconiqApiException extends Exception
{
    /**
     * @var string|null
     */
    protected $traceId;

    /**
     * @var string|null
     */
    protected $spanId;


    public function __construct(?string $traceId, ?string $spanId, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->traceId = $traceId;
        $this->spanId  = $spanId;
    }

    /**
     * @return string
     */
    public function getTraceId(): string
    {
        return $this->traceId;
    }

    /**
     * @return string
     */
    public function getSpanId(): string
    {
        return $this->spanId;
    }
}
