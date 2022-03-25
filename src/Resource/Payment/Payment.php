<?php
declare(strict_types = 1);

namespace Optios\Payconiq\Resource\Payment;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Payment
 * @package Payconiq\Resource\Payment
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Payment
{
    const STATUS_PENDING              = 'PENDING';
    const STATUS_IDENTIFIED           = 'IDENTIFIED';
    const STATUS_AUTHORIZED           = 'AUTHORIZED';
    const STATUS_AUTHORIZATION_FAILED = 'AUTHORIZATION_FAILED';
    const STATUS_SUCCEEDED            = 'SUCCEEDED';
    const STATUS_FAILED               = 'FAILED';
    const STATUS_CANCELLED            = 'CANCELLED';
    const STATUS_EXPIRED              = 'EXPIRED';

    const STATUS_TYPES = [
        self::STATUS_PENDING,
        self::STATUS_IDENTIFIED,
        self::STATUS_AUTHORIZED,
        self::STATUS_AUTHORIZATION_FAILED,
        self::STATUS_SUCCEEDED,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
        self::STATUS_EXPIRED,
    ];

    /**
     * @var string
     */
    private $paymentId;

    /**
     * @var Carbon
     */
    private $createdAt;

    /**
     * @var Carbon|null
     */
    private $expiresAt;

    /**
     * @var string|null
     */
    private $currency;

    /**
     * @var string
     */
    private $status;

    /**
     * @var Creditor
     */
    private $creditor;

    /**
     * @var Debtor|null
     */
    private $debtor;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var int|null
     */
    private $transferAmount;

    /**
     * @var int|null
     */
    private $tippingAmount;

    /**
     * @var int|null
     */
    private $totalAmount;

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
    private $selfLink;

    /**
     * @var string|null
     */
    private $deepLink;

    /**
     * @var string|null
     */
    private $qrLink;

    /**
     * @var string|null
     */
    private $refundLink;

    /**
     * @var string|null
     */
    private $checkoutLink;

    /**
     * @var string|null
     */
    private $reference;

    /**
     * Payment constructor.
     *
     * @param string $paymentId
     * @param Carbon $createdAt
     * @param string $status
     * @param int    $amount
     * @param string $currency
     */
    public function __construct(
        string $paymentId,
        Carbon $createdAt,
        string $status,
        int $amount,
        string $currency = 'EUR'
    ) {
        $this->paymentId = $paymentId;
        $this->createdAt = $createdAt;
        $this->status    = $status;
        $this->amount    = $amount;
        $this->currency  = $currency;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return static
     * @throws \Exception
     */
    public static function createFromResponse(ResponseInterface $response): self
    {
        $response = json_decode($response->getBody()->getContents());

        return Payment::createFromStdClass($response);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * @param \stdClass $response
     *
     * @return static
     * @throws \Exception
     */
    public static function createFromStdClass(\stdClass $response): self
    {
        $self = new self(
            $response->paymentId,
            new Carbon($response->createdAt),
            $response->status,
            $response->amount,
            $response->currency ?? 'EUR'
        );

        if (! empty($response->creditor)) {
            $self->setCreditor(Creditor::createFromStdClass($response->creditor));
        }

        if (! empty($response->debtor)) {
            $self->setDebtor(Debtor::createFromStdClass($response->debtor));
        }

        ! empty($response->expiresAt) ? $self->setExpiresAt(new Carbon($response->expiresAt)) : null;
        ! empty($response->transferAmount) ? $self->setTransferAmount($response->transferAmount) : null;
        ! empty($response->tippingAmount) ? $self->setTippingAmount($response->tippingAmount) : null;
        ! empty($response->totalAmount) ? $self->setTotalAmount($response->totalAmount) : null;
        ! empty($response->description) ? $self->setDescription($response->description) : null;
        ! empty($response->bulkId) ? $self->setBulkId($response->bulkId) : null;
        ! empty($response->reference) ? $self->setReference($response->reference) : null;

        ! empty($response->_links->self->href) ? $self->setSelfLink($response->_links->self->href) : null;
        ! empty($response->_links->deeplink->href) ? $self->setDeepLink($response->_links->deeplink->href) : null;
        ! empty($response->_links->qrcode->href) ? $self->setQrLink($response->_links->qrcode->href) : null;
        ! empty($response->_links->refund->href) ? $self->setRefundLink($response->_links->refund->href) : null;
        ! empty($response->_links->checkout->href) ? $self->setCheckoutLink($response->_links->checkout->href) : null;

        return $self;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'paymentId' => $this->paymentId,
            'createdAt' => $this->createdAt->toAtomString(),
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'creditor' => $this->creditor ? $this->creditor->toArray() : null,
            'debtor' => $this->debtor ? $this->debtor->toArray() : null,
            'expiresAt' => $this->expiresAt ? $this->expiresAt->toAtomString() : null,
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
            'reference' => $this->reference
        ];

        return array_filter($array);
    }

    /**
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * @param string $paymentId
     */
    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @param Carbon $createdAt
     */
    public function setCreatedAt(Carbon $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Carbon
     */
    public function getExpiresAt(): ?Carbon
    {
        return $this->expiresAt;
    }

    /**
     * @param Carbon $expiresAt
     */
    public function setExpiresAt(Carbon $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string|null $currency
     */
    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Creditor
     */
    public function getCreditor(): Creditor
    {
        return $this->creditor;
    }

    /**
     * @param Creditor $creditor
     */
    public function setCreditor(Creditor $creditor): void
    {
        $this->creditor = $creditor;
    }

    /**
     * @return Debtor|null
     */
    public function getDebtor(): ?Debtor
    {
        return $this->debtor;
    }

    /**
     * @param Debtor|null $debtor
     */
    public function setDebtor(?Debtor $debtor): void
    {
        $this->debtor = $debtor;
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
     * @return int|null
     */
    public function getTransferAmount(): ?int
    {
        return $this->transferAmount;
    }

    /**
     * @param int|null $transferAmount
     */
    public function setTransferAmount(?int $transferAmount): void
    {
        $this->transferAmount = $transferAmount;
    }

    /**
     * @return int|null
     */
    public function getTippingAmount(): ?int
    {
        return $this->tippingAmount;
    }

    /**
     * @param int|null $tippingAmount
     */
    public function setTippingAmount(?int $tippingAmount): void
    {
        $this->tippingAmount = $tippingAmount;
    }

    /**
     * @return int|null
     */
    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    /**
     * @param int|null $totalAmount
     */
    public function setTotalAmount(?int $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
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
    public function getSelfLink(): ?string
    {
        return $this->selfLink;
    }

    /**
     * @param string|null $selfLink
     */
    public function setSelfLink(?string $selfLink): void
    {
        $this->selfLink = $selfLink;
    }

    /**
     * @return string|null
     */
    public function getDeepLink(): ?string
    {
        return $this->deepLink;
    }

    /**
     * @param string|null $deepLink
     */
    public function setDeepLink(?string $deepLink): void
    {
        $this->deepLink = $deepLink;
    }

    /**
     * @return string|null
     */
    public function getQrLink(): ?string
    {
        return $this->qrLink;
    }

    /**
     * @param string|null $qrLink
     */
    public function setQrLink(?string $qrLink): void
    {
        $this->qrLink = $qrLink;
    }

    /**
     * @return string|null
     */
    public function getRefundLink(): ?string
    {
        return $this->refundLink;
    }

    /**
     * @param string|null $refundLink
     */
    public function setRefundLink(?string $refundLink): void
    {
        $this->refundLink = $refundLink;
    }

    /**
     * @return string|null
     */
    public function getCheckoutLink(): ?string
    {
        return $this->checkoutLink;
    }

    /**
     * @param string|null $checkoutLink
     */
    public function setCheckoutLink(?string $checkoutLink): void
    {
        $this->checkoutLink = $checkoutLink;
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
}
