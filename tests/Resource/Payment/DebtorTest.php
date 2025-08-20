<?php

namespace Tests\Optios\Payconiq\Resource\Payment;

use Optios\Payconiq\Resource\Payment\Debtor;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class DebtorTest extends TestCase
{
    use MatchesSnapshots;

    public function testDebtor(): void {
        $obj = new \stdClass();
        $debtor = Debtor::createFromObject($obj);

        $this->assertNull($debtor->getName());
        $this->assertNull($debtor->getIban());

        $this->assertMatchesJsonSnapshot($debtor->toArray());
    }
}
