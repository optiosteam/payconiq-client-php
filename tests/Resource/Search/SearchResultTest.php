<?php

namespace Tests\Optios\Payconiq\Resource\Search;

use GuzzleHttp\Psr7\Response;
use Optios\Payconiq\Resource\Search\SearchResult;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class SearchResultTest extends TestCase
{
    use MatchesSnapshots;

    public function testSearchResult(): void
    {
        $searchResult = SearchResult::createFromResponse(new Response(200, [], json_encode([
            'details' => [
                json_decode('{
    "paymentId": "new-payment-id",
    "createdAt": "2022-01-26T00:00:00+00:00",
    "status": "new-status",
    "amount": 20,
    "currency": "USD",
    "creditor": {
        "profileId": "profile-id2",
        "merchantId": "merchant-id",
        "name": "name",
        "iban": "iban"
    },
    "debtor": {
        "name": "name",
        "iban": "iban"
    },
    "expiresAt": "2022-01-26T00:00:00+00:00",
    "transferAmount": 10,
    "tippingAmount": 10,
    "totalAmount": 10,
    "description": "desc",
    "bulkId": "bulk-id",
    "selfLink": "some-uri",
    "deepLink": "some-uri",
    "qrLink": "some-uri",
    "refundLink": "some-uri"
}', true),
            ],
            'size' => 1,
            'totalPages' => 2,
            'totalElements' => 3,
            'number' => 4,
        ])));

        $this->assertEquals(1, $searchResult->getSize());
        $searchResult->setSize(2);
        $this->assertEquals(2, $searchResult->getSize());

        $this->assertEquals(2, $searchResult->getTotalPages());
        $searchResult->setTotalPages(3);
        $this->assertEquals(3, $searchResult->getTotalPages());

        $this->assertEquals(3, $searchResult->getTotalElements());
        $searchResult->setTotalElements(4);
        $this->assertEquals(4, $searchResult->getTotalElements());

        $this->assertEquals(4, $searchResult->getNumber());
        $searchResult->setNumber(5);
        $this->assertEquals(5, $searchResult->getNumber());

        $this->assertMatchesJsonSnapshot($searchResult->toArray());

        $searchResult->setDetails([]);
        $this->assertEmpty($searchResult->getDetails());
    }
}
