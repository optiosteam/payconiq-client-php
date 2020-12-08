<?php
declare(strict_types = 1);

namespace Optios\Payconiq\Request;

/**
 * Class RequestPayment
 * @package Payconiq\Request
 */
class RequestPayment
{
    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string|null
     */
    private $callbackUrl;

    /**
     * @var string|null
     */
    private $reference;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $bulkId;

    /**
     * Used for Static QR Sticker
     * @var string|null
     */
    private $posId;

    /**
     * Used for Static QR Sticker
     * @var string|null
     */
    private $shopId;

    /**
     * Used for Static QR Sticker
     * @var string|null
     */
    private $shopName;

    /**
     * Used for Checkout Flow Online
     * @var string|null
     */
    private $returnUrl;

    /**
     * RequestPayment constructor.
     *
     * @param int    $amount
     * @param string $currency
     */
    public function __construct(
        int $amount,
        string $currency = 'EUR'
    ) {
        $this->amount   = $amount;
        $this->currency = $currency;
    }

    /**
     * @param int    $amount
     * @param string $posId
     * @param string $currency
     *
     * @return RequestPayment
     */
    public static function createForStaticQR(int $amount, string $posId, string $currency = 'EUR'): self
    {
        $self = new self($amount, $currency);
        $self->setPosId($posId);

        return $self;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];

        $this->callbackUrl ? $array[ 'callbackUrl' ] = $this->callbackUrl : null;
        $this->reference ? $array[ 'reference' ] = $this->reference : null;
        $this->description ? $array[ 'description' ] = $this->description : null;
        $this->bulkId ? $array[ 'bulkId' ] = $this->bulkId : null;
        $this->posId ? $array[ 'posId' ] = $this->posId : null;
        $this->shopId ? $array[ 'shopId' ] = $this->shopId : null;
        $this->shopName ? $array[ 'shopName' ] = $this->shopName : null;
        $this->returnUrl ? $array[ 'returnUrl' ] = $this->returnUrl : null;

        return $array;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string|null
     */
    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    /**
     * @param string|null $callbackUrl
     */
    public function setCallbackUrl(?string $callbackUrl): void
    {
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string|null $reference
     */
    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getBulkId(): ?string
    {
        return $this->bulkId;
    }

    /**
     * @param string|null $bulkId
     */
    public function setBulkId(?string $bulkId): void
    {
        $this->bulkId = $bulkId;
    }

    /**
     * @return string|null
     */
    public function getPosId(): ?string
    {
        return $this->posId;
    }

    /**
     * @param string|null $posId
     */
    public function setPosId(?string $posId): void
    {
        $this->posId = $posId;
    }

    /**
     * @return string|null
     */
    public function getShopId(): ?string
    {
        return $this->shopId;
    }

    /**
     * @param string|null $shopId
     */
    public function setShopId(?string $shopId): void
    {
        $this->shopId = $shopId;
    }

    /**
     * @return string|null
     */
    public function getShopName(): ?string
    {
        return $this->shopName;
    }

    /**
     * @param string|null $shopName
     */
    public function setShopName(?string $shopName): void
    {
        $this->shopName = $shopName;
    }

    /**
     * @return string|null
     */
    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
    }

    /**
     * @param string|null $returnUrl
     */
    public function setReturnUrl(?string $returnUrl): void
    {
        $this->returnUrl = $returnUrl;
    }
}
