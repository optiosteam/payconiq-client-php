<?php
declare(strict_types=1);

namespace Optios\Payconiq\Resource\Payment;

use Carbon\CarbonImmutable;
use Optios\Payconiq\Enum\PaymentStatus;
use Psr\Http\Message\ResponseInterface;

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
        private ?int $transferAmount = null,
        private ?int $tippingAmount = null,
        private ?int $totalAmount = null,
        private ?string $description = null,
        private ?string $bulkId = null,
        private ?string $selfLink = null,
        private ?string $deepLink = null,
        private ?string $qrLink = null,
        private ?string $refundLink = null,
        private ?string $checkoutLink = null,
        private ?string $reference = null,
    )
    {
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
     */
    public static function createFromObject(object $r): self
    {
        $expiresAt = (isset($r->expiresAt) && $r->expiresAt !== '')
            ? new CarbonImmutable((string)$r->expiresAt)
            : null;

        $transferAmount = isset($r->transferAmount) ? (int)$r->transferAmount : null;
        $tippingAmount = isset($r->tippingAmount) ? (int)$r->tippingAmount : null;
        $totalAmount = isset($r->totalAmount) ? (int)$r->totalAmount : null;

        $description = isset($r->description) ? (string)$r->description : null;
        $bulkId = isset($r->bulkId) ? (string)$r->bulkId : null;
        $reference = isset($r->reference) ? (string)$r->reference : null;

        $creditor = isset($r->creditor)
            ? Creditor::createFromObject($r->creditor)
            : null;

        $debtor = isset($r->debtor)
            ? Debtor::createFromObject($r->debtor)
            : null;

        $selfLink = $r->_links?->self?->href ?? null;
        $deepLink = $r->_links?->deeplink?->href ?? null;
        $qrLink = $r->_links?->qrcode?->href ?? null;
        $refundLink = $r->_links?->refund?->href ?? null;
        $checkoutLink = $r->_links?->checkout?->href ?? null;

        return new self(
            paymentId: $r->paymentId,
            createdAt: new CarbonImmutable($r->createdAt),
            status: PaymentStatus::from($r->status),
            amount: $r->amount,
            currency: $r->currency ?? 'EUR',
            expiresAt: $expiresAt,
            creditor: $creditor,
            debtor: $debtor,
            transferAmount: $transferAmount,
            tippingAmount: $tippingAmount,
            totalAmount: $totalAmount,
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
            'transferAmount' => $this->transferAmount,
            'tippingAmount' => $this->tippingAmount,
            'totalAmount' => $this->totalAmount,
            'description' => $this->description,
            'bulkId' => $this->bulkId,
            'selfLink' => $this->selfLink,
            'deepLink' => $this->deepLink,
            'qrLink' => $this->qrLink,
            'refundLink' => $this->refundLink,
            'checkoutLink' => $this->checkoutLink,
            'reference' => $this->reference,
        ];

//        return array_filter($array);
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

    public function getTransferAmount(): ?int
    {
        return $this->transferAmount;
    }

    public function getTippingAmount(): ?int
    {
        return $this->tippingAmount;
    }

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
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
