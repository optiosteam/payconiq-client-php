<?php
declare(strict_types = 1);

namespace Optios\Payconiq\Request;

use Carbon\Carbon;

/**
 * Class SearchPayments
 * @package Payconiq\Request
 */
class SearchPayments
{
    private const SEARCH_DATE_FORMAT = 'Y-m-d\TH:i:s.v\Z';

    /**
     * @var Carbon
     */
    private $from;

    /**
     * @var Carbon|null
     */
    private $to;

    /**
     * @var string[]
     */
    private $paymentStatuses;

    /**
     * @var string|null
     */
    private $reference;

    /**
     * SearchPayments constructor.
     *
     * @param \DateTime $from
     */
    public function __construct(\DateTime $from)
    {
        $this->setFrom($from);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'from' => $this->from->format(self::SEARCH_DATE_FORMAT),
        ];

        if (null !== $this->to) {
            $array[ 'to' ] = $this->to->format(self::SEARCH_DATE_FORMAT);
        }

        if (! empty($this->paymentStatuses)) {
            $array[ 'paymentStatuses' ] = $this->paymentStatuses;
        }

        if (! empty($this->reference)) {
            $array[ 'reference' ] = $this->reference;
        }

        return $array;
    }

    /**
     * @return Carbon
     */
    public function getFrom(): Carbon
    {
        return $this->from;
    }

    /**
     * @param \DateTime $from
     */
    public function setFrom(\DateTime $from): void
    {
        if (! $from instanceof Carbon) {
            $from = new Carbon($from);
        }

        $this->from = $from->clone()->setTimezone('UTC');
    }

    /**
     * @return Carbon|null
     */
    public function getTo(): ?Carbon
    {
        return $this->to;
    }

    /**
     * @param \DateTime|null $to
     */
    public function setTo(?\DateTime $to): void
    {
        if (! $to instanceof Carbon) {
            $to = new Carbon($to);
        }

        $this->to = $to->clone()->setTimezone('UTC');
    }

    /**
     * @return string[]
     */
    public function getPaymentStatuses(): array
    {
        return $this->paymentStatuses;
    }

    /**
     * @param string[] $paymentStatuses
     */
    public function setPaymentStatuses(array $paymentStatuses): void
    {
        $this->paymentStatuses = $paymentStatuses;
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
