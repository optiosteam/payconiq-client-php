<?php
declare(strict_types = 1);

namespace Payconiq\Resource\Payment;

/**
 * Class Creditor
 * @package Payconiq\Resource\Payment
 */
class Creditor
{
    /**
     * @var string
     */
    private $profileId;

    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $iban;

    /**
     * @var string|null
     */
    private $callbackUrl;

    /**
     * Creditor constructor.
     *
     * @param string      $profileId
     * @param string      $merchantId
     * @param string      $name
     * @param string      $iban
     * @param string|null $callbackUrl
     */
    public function __construct(string $profileId, string $merchantId, string $name, string $iban, ?string $callbackUrl)
    {
        $this->profileId   = $profileId;
        $this->merchantId  = $merchantId;
        $this->name        = $name;
        $this->iban        = $iban;
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * @return string
     */
    public function getProfileId(): string
    {
        return $this->profileId;
    }

    /**
     * @param string $profileId
     */
    public function setProfileId(string $profileId): void
    {
        $this->profileId = $profileId;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     */
    public function setMerchantId(string $merchantId): void
    {
        $this->merchantId = $merchantId;
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

    /**
     * @return string|null
     */
    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    /**
     * @param string|null $callbackUrl
     */
    public function setCallbackUrl(string $callbackUrl): void
    {
        $this->callbackUrl = $callbackUrl;
    }
}
