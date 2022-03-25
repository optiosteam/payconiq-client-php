<?php

namespace Tests\Optios\Payconiq\Resource\Payment;

use Optios\Payconiq\Resource\Payment\Creditor;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CreditorTest extends TestCase
{
    use MatchesSnapshots;

    public function testCreditor(): void
    {
        $obj = new \stdClass();
        $obj->profileId = 'profile-id';
        $obj->merchantId = 'merchant-id';
        $obj->name = 'name';
        $obj->iban = 'iban';

        $creditor = Creditor::createFromStdClass($obj);

        $this->assertEquals('profile-id', $creditor->getProfileId());
        $creditor->setProfileId('new-profile-id');
        $this->assertEquals('new-profile-id', $creditor->getProfileId());

        $this->assertEquals('merchant-id', $creditor->getMerchantId());
        $creditor->setMerchantId('new-merchant-id');
        $this->assertEquals('new-merchant-id', $creditor->getMerchantId());

        $this->assertEquals('name', $creditor->getName());
        $creditor->setName('new-name');
        $this->assertEquals('new-name', $creditor->getName());

        $this->assertEquals('iban', $creditor->getIban());
        $creditor->setIban('new-iban');
        $this->assertEquals('new-iban', $creditor->getIban());

        $this->assertNull($creditor->getCallbackUrl());
        $creditor->setCallbackUrl('some-uri');
        $this->assertEquals('some-uri', $creditor->getCallbackUrl());

        $this->assertMatchesJsonSnapshot($creditor->toArray());
    }
}