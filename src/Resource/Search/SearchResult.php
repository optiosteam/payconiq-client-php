<?php
declare(strict_types = 1);

namespace Optios\Payconiq\Resource\Search;

use Optios\Payconiq\Resource\Payment\Payment;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SearchResult
 * @package Optios\Payconiq\Resource\Search
 */
class SearchResult
{
    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $totalPages;

    /**
     * @var int
     */
    private $totalElements;

    /**
     * @var int
     */
    private $number;

    /**
     * @var Payment[]
     */
    private $details;

    /**
     * SearchResult constructor.
     *
     * @param int       $size
     * @param int       $totalPages
     * @param int       $totalElements
     * @param int       $number
     * @param Payment[] $details
     */
    public function __construct(int $size, int $totalPages, int $totalElements, int $number, array $details)
    {
        $this->size          = $size;
        $this->totalPages    = $totalPages;
        $this->totalElements = $totalElements;
        $this->number        = $number;
        $this->details       = $details;
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

        $details = [];

        if (! empty($response->details)) {
            foreach ($response->details as $paymentDetail) {
                $details[] = Payment::createFromStdClass($paymentDetail);
            }
        }

        return new self(
            $response->size,
            $response->totalPages,
            $response->totalElements,
            $response->number,
            $details
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'size' => $this->size,
            'totalPages' => $this->totalPages,
            'totalElements' => $this->totalElements,
            'number' => $this->number,
        ];

        $details = [];
        foreach ($this->details as $payment) {
            $details[] = $payment->toArray();
        }

        $array[ 'details' ] = $details;

        return $array;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @param int $totalPages
     */
    public function setTotalPages(int $totalPages): void
    {
        $this->totalPages = $totalPages;
    }

    /**
     * @return int
     */
    public function getTotalElements(): int
    {
        return $this->totalElements;
    }

    /**
     * @param int $totalElements
     */
    public function setTotalElements(int $totalElements): void
    {
        $this->totalElements = $totalElements;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return Payment[]
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * @param Payment[] $details
     */
    public function setDetails(array $details): void
    {
        $this->details = $details;
    }
}
