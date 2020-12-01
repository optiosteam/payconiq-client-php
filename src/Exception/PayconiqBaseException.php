<?php
declare(strict_types = 1);

namespace Optios\Payconiq\Exception;

/**
 * Class PayconiqBaseException
 * @package Optios\Payconiq\Exception
 */
abstract class PayconiqBaseException extends \Exception
{
    /**
     * @var bool
     */
    protected $useProd;

    /**
     * PayconiqBaseException constructor.
     *
     * @param bool $useProd
     */
    public function __construct(bool $useProd = true, $message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->useProd = $useProd;
    }

    /**
     * @return bool
     */
    public function isUseProd(): bool
    {
        return $this->useProd;
    }
}
