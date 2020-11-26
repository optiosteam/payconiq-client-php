<?php
declare(strict_types = 1);

namespace Payconiq\Request;

/**
 * Class CreatePayment
 * @package Payconiq\Request
 */
class CreatePayment
{
    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $posId;

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
     * @var string|null
     */
    private $shopId;

    /**
     * @var string|null
     */
    private $shopName;

    /**
     * CreatePayment constructor.
     *
     * @param int         $amount
     * @param string      $posId
     * @param string      $currency
     * @param string|null $callbackUrl
     * @param string|null $reference
     * @param string|null $description
     * @param string|null $bulkId
     * @param string|null $shopId
     * @param string|null $shopName
     */
    public function __construct(
        int $amount,
        string $posId,
        string $currency = 'EUR',
        ?string $callbackUrl = null,
        ?string $reference = null,
        ?string $description = null,
        ?string $bulkId = null,
        ?string $shopId = null,
        ?string $shopName = null
    ) {
        $this->amount      = $amount;
        $this->posId       = $posId;
        $this->currency    = $currency;
        $this->callbackUrl = $callbackUrl;
        $this->reference   = $reference;
        $this->description = $description;
        $this->bulkId      = $bulkId;
        $this->shopId      = $shopId;
        $this->shopName    = $shopName;
    }

    public function toArray(): array
    {
        $array = [
            'amount' => $this->amount,
            'posId' => $this->posId,
            'currency' => $this->currency,
        ];

        $this->callbackUrl ? $array[ 'callbackUrl' ] = $this->callbackUrl : null;
        $this->reference ? $array[ 'reference' ] = $this->reference : null;
        $this->description ? $array[ 'description' ] = $this->description : null;
        $this->bulkId ? $array[ 'bulkId' ] = $this->bulkId : null;
        $this->shopId ? $array[ 'shopId' ] = $this->shopId : null;
        $this->shopName ? $array[ 'shopName' ] = $this->shopName : null;

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
    public function getPosId(): string
    {
        return $this->posId;
    }

    /**
     * @param string $posId
     */
    public function setPosId(string $posId): void
    {
        $this->posId = $posId;
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
}
