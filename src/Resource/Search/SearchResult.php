<?php
declare(strict_types=1);

namespace Optios\Payconiq\Resource\Search;

use Optios\Payconiq\Resource\Payment\Payment;
use Psr\Http\Message\ResponseInterface;

final readonly class SearchResult
{
    /**
     * @param array<Payment> $details
     */
    private function __construct(
        private int $size,
        private int $totalPages,
        private int $totalElements,
        private int $number,
        private array $details,
    )
    {
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public static function createFromResponse(ResponseInterface $response): self
    {
        $response = json_decode(
            json: $response->getBody()->getContents(),
            associative: false,
            flags: JSON_THROW_ON_ERROR,
        );

        $details = [];

        if (false === empty($response->details)) {
            foreach ($response->details as $paymentDetail) {
                $details[] = Payment::createFromObject($paymentDetail);
            }
        }

        return new self(
            $response->size,
            $response->totalPages,
            $response->totalElements,
            $response->number,
            $details,
        );
    }

    public function toArray(): array
    {
//        $array = [
//            'size' => $this->size,
//            'totalPages' => $this->totalPages,
//            'totalElements' => $this->totalElements,
//            'number' => $this->number,
//        ];
//
//        $details = [];
//        foreach ($this->details as $payment) {
//            $details[] = $payment->toArray();
//        }
//
//        $array['details'] = $details;
//
//        return $array;

        return [
            'size' => $this->size,
            'totalPages' => $this->totalPages,
            'totalElements' => $this->totalElements,
            'number' => $this->number,
            'details' => array_map(
                callback: static fn(Payment $p) => $p->toArray(),
                array: $this->details,
            ),
        ];
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getTotalElements(): int
    {
        return $this->totalElements;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @return array<Payment>
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}
