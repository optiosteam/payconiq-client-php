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
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $iban;

    /**
     * Debtor constructor.
     *
     * @param string|null $name
     * @param string|null $iban
     */
    public function __construct(?string $name, ?string $iban)
    {
        $this->name = $name;
        $this->iban = $iban;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];

        $this->name ? $array[ 'name' ] = $this->name : null;
        $this->iban ? $array[ 'iban' ] = $this->iban : null;

        return $array;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getIban(): ?string
    {
        return $this->iban;
    }

    /**
     * @param string|null $iban
     */
    public function setIban(?string $iban): void
    {
        $this->iban = $iban;
    }
}
