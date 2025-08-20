<?php

namespace Tests\Optios\Payconiq\Resource\Payment;

use Optios\Payconiq\Resource\Payment\Creditor;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CreditorTest extends TestCase
{
    use MatchesSnapshots;

    public function testCreditor(): void {
        $obj = new \stdClass();
        $obj->profileId = 'profile-id';
        $obj->merchantId = 'merchant-id';
        $obj->name = 'name';
        $obj->iban = 'iban';

        $creditor = Creditor::createFromObject($obj);

        $this->assertEquals('profile-id', $creditor->getProfileId());
        $this->assertEquals('merchant-id', $creditor->getMerchantId());
        $this->assertEquals('name', $creditor->getName());
        $this->assertEquals('iban', $creditor->getIban());
        $this->assertNull($creditor->getCallbackUrl());

        $this->assertMatchesJsonSnapshot($creditor->toArray());
    }
}
