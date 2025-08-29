<?php

declare(strict_types=1);

namespace Optios\Payconiq\Resource\Payment;

final readonly class Creditor
{
    private function __construct(
        private string $profileId,
        private string $merchantId,
        private string $name,
        private string $iban,
        private ?string $callbackUrl,
    ) {
    }

    /**
     * @deprecated Use createFromObject() instead.
     */
    public static function createFromStdClass(\stdClass $class): self
    {
        return self::createFromObject($class);
    }

    public static function createFromObject(object $obj): self
    {
        return new self(
            profileId: $obj->profileId,
            merchantId: $obj->merchantId,
            name: $obj->name,
            iban: $obj->iban,
            callbackUrl: $obj->callbackUrl ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'profileId' => $this->profileId,
            'merchantId' => $this->merchantId,
            'name' => $this->name,
            'iban' => $this->iban,
            'callbackUrl' => $this->callbackUrl,
        ];
    }

    public function getProfileId(): string
    {
        return $this->profileId;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIban(): string
    {
        return $this->iban;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }
}
