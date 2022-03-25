<?php

namespace Tests\Optios\Payconiq\Resource\Payment;

use Optios\Payconiq\Resource\Payment\Debtor;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class DebtorTest extends TestCase
{
    use MatchesSnapshots;

    public function testDebtor(): void{
        $obj    = new \stdClass();
        $debtor = Debtor::createFromStdClass($obj);

        $this->assertNull($debtor->getName());
        $debtor->setName('new-name');
        $this->assertEquals('new-name', $debtor->getName());

        $this->assertNull($debtor->getIban());
        $debtor->setIban('new-iban');
        $this->assertEquals('new-iban', $debtor->getIban());

        $this->assertMatchesJsonSnapshot($debtor->toArray());
    }
}
