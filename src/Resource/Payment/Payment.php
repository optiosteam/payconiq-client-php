<?php

declare(strict_types=1);

namespace Optios\Payconiq\Resource\Payment;

use Carbon\CarbonImmutable;
use Optios\Payconiq\Enum\PaymentStatus;
use Psr\Http\Message\ResponseInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
final readonly class Payment
{
    private function __construct(
        private string $paymentId,
        private CarbonImmutable $createdAt,
        private PaymentStatus $status,
        private int $amount,
        private string $currency,
        private ?CarbonImmutable $expiresAt = null,
        private ?Creditor $creditor = null,
        private ?Debtor $debtor = null,
        private ?string $description = null,
        private ?string $bulkId = null,
        private ?string $selfLink = null,
        private ?string $deepLink = null,
        private ?string $qrLink = null,
        private ?string $refundLink = null,
        private ?string $checkoutLink = null,
        private ?string $reference = null,
    ) {
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public static function createFromResponse(ResponseInterface $response): self
    {
        $decoded = json_decode(
            json: $response->getBody()->getContents(),
            associative: false,
            flags: JSON_THROW_ON_ERROR,
        );

        return self::createFromObject($decoded);
    }

    /**
     * @throws \Exception
     * @deprecated Use createFromObject() instead.
     */
    public static function createFromStdClass(\stdClass $response): self
    {
        return self::createFromObject($response);
    }

    /**
     * @throws \ValueError if status is unknown (via PaymentStatus::from).
     * @throws \Exception  if date parsing fails.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * phpcs:disable Generic.Metrics.CyclomaticComplexity
     */
    public static function createFromObject(object $obj): self
    {
        $expiresAt = (isset($obj->expiresAt) && $obj->expiresAt !== '')
            ? new CarbonImmutable((string) $obj->expiresAt)
            : null;

        $description = isset($obj->description) ? (string) $obj->description : null;
        $bulkId = isset($obj->bulkId) ? (string) $obj->bulkId : null;
        $reference = isset($obj->reference) ? (string) $obj->reference : null;

        $creditor = isset($obj->creditor)
            ? Creditor::createFromObject($obj->creditor)
            : null;

        $debtor = isset($obj->debtor)
            ? Debtor::createFromObject($obj->debtor)
            : null;

        $selfLink = $obj->_links?->self?->href ?? null;
        $deepLink = $obj->_links?->deeplink?->href ?? null;
        $qrLink = $obj->_links?->qrcode?->href ?? null;
        $refundLink = $obj->_links?->refund?->href ?? null;
        $checkoutLink = $obj->_links?->checkout?->href ?? null;

        return new self(
            paymentId: $obj->paymentId,
            createdAt: new CarbonImmutable($obj->createdAt),
            status: PaymentStatus::from($obj->status),
            amount: $obj->amount,
            currency: $obj->currency ?? 'EUR',
            expiresAt: $expiresAt,
            creditor: $creditor,
            debtor: $debtor,
            description: $description,
            bulkId: $bulkId,
            selfLink: $selfLink,
            deepLink: $deepLink,
            qrLink: $qrLink,
            refundLink: $refundLink,
            checkoutLink: $checkoutLink,
            reference: $reference,
        );
    }
    // phpcs:enable Generic.Metrics.CyclomaticComplexity

    public function toArray(): array
    {
        return [
            'paymentId' => $this->paymentId,
            'createdAt' => $this->createdAt->toAtomString(),
            'status' => $this->status->value,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'creditor' => $this->creditor?->toArray(),
            'debtor' => $this->debtor?->toArray(),
            'expiresAt' => $this->expiresAt?->toAtomString(),
            'description' => $this->description,
            'bulkId' => $this->bulkId,
            'selfLink' => $this->selfLink,
            'deepLink' => $this->deepLink,
            'qrLink' => $this->qrLink,
            'refundLink' => $this->refundLink,
            'checkoutLink' => $this->checkoutLink,
            'reference' => $this->reference,
        ];
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getExpiresAt(): ?CarbonImmutable
    {
        return $this->expiresAt;
    }

    public function getCreditor(): ?Creditor
    {
        return $this->creditor;
    }

    public function getDebtor(): ?Debtor
    {
        return $this->debtor;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getBulkId(): ?string
    {
        return $this->bulkId;
    }

    public function getSelfLink(): ?string
    {
        return $this->selfLink;
    }

    public function getDeepLink(): ?string
    {
        return $this->deepLink;
    }

    public function getQrLink(): ?string
    {
        return $this->qrLink;
    }

    public function getRefundLink(): ?string
    {
        return $this->refundLink;
    }

    public function getCheckoutLink(): ?string
    {
        return $this->checkoutLink;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }
}
