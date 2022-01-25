<?php

namespace Tests\Optios\Payconiq\Request;

use Carbon\Carbon;
use Optios\Payconiq\Request\SearchPayments;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class SearchPaymentsTest extends TestCase
{
    use MatchesSnapshots;

    public function testSearchPayments(): void{
        $searchPayments = new SearchPayments(new \DateTime('2022-01-25'));

        $this->assertEquals(new Carbon('2022-01-25'), $searchPayments->getFrom());

        $this->assertNull($searchPayments->getTo());
        $searchPayments->setTo(new \DateTime('2022-01-26'));
        $this->assertEquals(new Carbon('2022-01-26'), $searchPayments->getTo());

        $this->assertEmpty($searchPayments->getPaymentStatuses());
        $searchPayments->setPaymentStatuses(['status', 'status-two']);
        $this->assertEquals(['status', 'status-two'], $searchPayments->getPaymentStatuses());

        $this->assertNull($searchPayments->getReference());
        $searchPayments->setReference('ref');
        $this->assertEquals('ref', $searchPayments->getReference());

        $this->assertMatchesJsonSnapshot($searchPayments->toArray());
    }
}