<?php
declare(strict_types=1);

namespace Optios\Payconiq\Request;

use Carbon\CarbonImmutable;
use Optios\Payconiq\Enum\PaymentStatus;

final class SearchPayments
{
    private const SEARCH_DATE_FORMAT = 'Y-m-d\TH:i:s.v\Z';

    private readonly CarbonImmutable $from;
    private ?CarbonImmutable $to = null;
    /**
     * @var array<PaymentStatus>
     */
    private array $paymentStatuses = [];
    private ?string $reference = null;

    public function __construct(\DateTime $from)
    {
        $this->setFrom($from);
    }

    public function toArray(): array
    {
        $array = [
            'from' => $this->from->format(self::SEARCH_DATE_FORMAT),
        ];

        if (null !== $this->to) {
            $array['to'] = $this->to->format(self::SEARCH_DATE_FORMAT);
        }

        if (false === empty($this->paymentStatuses)) {
            $array['paymentStatuses'] = array_map(
                callback: static fn(PaymentStatus $s) => $s->value,
                array: $this->paymentStatuses,
            );
        }

        if (null !== $this->reference && '' !== $this->reference) {
            $array['reference'] = $this->reference;
        }

        return $array;
    }

    public function getFrom(): CarbonImmutable
    {
        return $this->from;
    }

    public function setFrom(\DateTime $from): self
    {
        if (false === $from instanceof CarbonImmutable) {
            $from = new CarbonImmutable($from);
        }

        $this->from = $from->setTimezone('UTC');

        return $this;
    }

    public function getTo(): ?CarbonImmutable
    {
        return $this->to;
    }

    public function setTo(?\DateTime $to): self
    {
        if (null === $to) {
            $this->to = null;

            return $this;
        }

        if (false === $to instanceof CarbonImmutable) {
            $to = new CarbonImmutable($to);
        }

        $this->to = $to->setTimezone('UTC');

        return $this;
    }

    /**
     * @return array<PaymentStatus>
     */
    public function getPaymentStatuses(): array
    {
        return $this->paymentStatuses;
    }

    /**
     * @param array<PaymentStatus|string> $paymentStatuses
     */
    public function setPaymentStatuses(array $paymentStatuses): self
    {
        $this->paymentStatuses = array_map(
            callback: static fn($s) => $s instanceof PaymentStatus ? $s : PaymentStatus::from((string)$s),
            array: $paymentStatuses
        );
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }
}
