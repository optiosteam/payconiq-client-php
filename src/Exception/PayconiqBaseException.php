<?php
declare(strict_types=1);

namespace Optios\Payconiq\Exception;

abstract class PayconiqBaseException extends \Exception
{
    public function __construct(
        protected bool $useProd = true,
        $message = "",
        $code = 0, \Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function isUseProd(): bool {
        return $this->useProd;
    }
}
