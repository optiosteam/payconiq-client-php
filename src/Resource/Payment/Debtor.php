<?php
declare(strict_types = 1);

namespace Optios\Payconiq\Resource\Payment;

/**
 * Class Debtor
 * @package Payconiq\Resource\Payment
 */
class Debtor
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $iban;

    /**
     * Debtor constructor.
     *
     * @param string $name
     * @param string $iban
     */
    public function __construct(string $name, string $iban)
    {
        $this->name = $name;
        $this->iban = $iban;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIban(): string
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     */
    public function setIban(string $iban): void
    {
        $this->iban = $iban;
    }
}
