<?php
declare(strict_types=1);

namespace Optios\Payconiq\Request;

final class RequestPayment
{
    private readonly int $amount;
    private readonly string $currency;
    private ?string $callbackUrl = null;
    private ?string $reference = null;
    private ?string $description = null;
    private ?string $bulkId = null;
    private ?string $posId = null; // Used for Static QR Sticker
    private ?string $shopId = null; // Used for Static QR Sticker
    private ?string $shopName = null; // Used for Static QR Sticker
    private ?string $returnUrl = null; // Used for Checkout Flow Online

    public function __construct(
        int $amount,
        string $currency = 'EUR',
    ) {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function createForStaticQR(int $amount, string $posId, string $currency = 'EUR'): self {
        $self = new self(
            amount: $amount,
            currency: $currency,
        );
        $self->setPosId($posId);

        return $self;
    }

    public function toArray(): array {
        $array = [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'callbackUrl' => $this->callbackUrl,
            'reference' => $this->reference,
            'description' => $this->description,
            'bulkId' => $this->bulkId,
            'posId' => $this->posId,
            'shopId' => $this->shopId,
            'shopName' => $this->shopName,
            'returnUrl' => $this->returnUrl,
        ];

        return array_filter($array);
    }

    public function getAmount(): int {
        return $this->amount;
    }

    public function getCurrency(): string {
        return $this->currency;
    }

    public function getCallbackUrl(): ?string {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(?string $callbackUrl): self {
        $this->callbackUrl = $callbackUrl;

        return $this;
    }

    public function getReference(): ?string {
        return $this->reference;
    }

    public function setReference(?string $reference): self {
        $this->reference = $reference;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;

        return $this;
    }

    public function getBulkId(): ?string {
        return $this->bulkId;
    }

    public function setBulkId(?string $bulkId): self {
        $this->bulkId = $bulkId;

        return $this;
    }

    public function getPosId(): ?string {
        return $this->posId;
    }

    public function setPosId(?string $posId): self {
        $this->posId = $posId;

        return $this;
    }

    public function getShopId(): ?string {
        return $this->shopId;
    }

    public function setShopId(?string $shopId): self {
        $this->shopId = $shopId;

        return $this;
    }

    public function getShopName(): ?string {
        return $this->shopName;
    }

    public function setShopName(?string $shopName): self {
        $this->shopName = $shopName;

        return $this;
    }

    public function getReturnUrl(): ?string {
        return $this->returnUrl;
    }

    public function setReturnUrl(?string $returnUrl): self {
        $this->returnUrl = $returnUrl;

        return $this;
    }
}
