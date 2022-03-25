<?php

namespace Tests\Optios\Payconiq\Resource\Payment;

use Carbon\Carbon;
use Optios\Payconiq\Resource\Payment\Creditor;
use Optios\Payconiq\Resource\Payment\Debtor;
use Optios\Payconiq\Resource\Payment\Payment;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class PaymentTest extends TestCase
{
    use MatchesSnapshots;

    public function testPayment(): void
    {
        $obj                       = new \stdClass();
        $obj->paymentId            = 'payment-id';
        $obj->createdAt            = '2022-01-25';
        $obj->status               = 'status';
        $obj->amount               = 10;
        $obj->creditor             = new \stdClass();
        $obj->creditor->profileId  = 'profile-id';
        $obj->creditor->merchantId = 'merchant-id';
        $obj->creditor->name       = 'name';
        $obj->creditor->iban       = 'iban';
        $obj->debtor               = new \stdClass();

        $payment = Payment::createFromStdClass($obj);

        $this->assertEquals('payment-id', $payment->getPaymentId());
        $payment->setPaymentId('new-payment-id');
        $this->assertEquals('new-payment-id', $payment->getPaymentId());

        $this->assertEquals(new Carbon('2022-01-25'), $payment->getCreatedAt());
        $payment->setCreatedAt(new Carbon('2022-01-26'));
        $this->assertEquals(new Carbon('2022-01-26'), $payment->getCreatedAt());

        $this->assertNull($payment->getExpiresAt());
        $payment->setExpiresAt(new Carbon('2022-01-26'));
        $this->assertEquals(new Carbon('2022-01-26'), $payment->getExpiresAt());

        $this->assertEquals('EUR', $payment->getCurrency());
        $payment->setCurrency('USD');
        $this->assertEquals('USD', $payment->getCurrency());

        $this->assertEquals('status', $payment->getStatus());
        $payment->setStatus('new-status');
        $this->assertEquals('new-status', $payment->getStatus());

        $this->assertEquals(new Creditor('profile-id', 'merchant-id', 'name', 'iban', ''), $payment->getCreditor());
        $payment->setCreditor(new Creditor('profile-id2', 'merchant-id', 'name', 'iban', ''));
        $this->assertEquals(new Creditor('profile-id2', 'merchant-id', 'name', 'iban', ''), $payment->getCreditor());

        $this->assertEquals(new Debtor(null, null), $payment->getDebtor());
        $payment->setDebtor(new Debtor('name', 'iban'));
        $this->assertEquals(new Debtor('name', 'iban'), $payment->getDebtor());

        $this->assertEquals(10, $payment->getAmount());
        $payment->setAmount(20);
        $this->assertEquals(20, $payment->getAmount());

        $this->assertNull($payment->getTransferAmount());
        $payment->setTransferAmount(10);
        $this->assertEquals(10, $payment->getTransferAmount());

        $this->assertNull($payment->getTippingAmount());
        $payment->setTippingAmount(10);
        $this->assertEquals(10, $payment->getTippingAmount());

        $this->assertNull($payment->getTotalAmount());
        $payment->setTotalAmount(10);
        $this->assertEquals(10, $payment->getTotalAmount());

        $this->assertNull($payment->getDescription());
        $payment->setDescription('desc');
        $this->assertEquals('desc', $payment->getDescription());

        $this->assertNull($payment->getBulkId());
        $payment->setBulkId('bulk-id');
        $this->assertEquals('bulk-id', $payment->getBulkId());

        $this->assertNull($payment->getSelfLink());
        $payment->setSelfLink('some-uri');
        $this->assertEquals('some-uri', $payment->getSelfLink());

        $this->assertNull($payment->getDeepLink());
        $payment->setDeepLink('some-uri');
        $this->assertEquals('some-uri', $payment->getDeepLink());

        $this->assertNull($payment->getQrLink());
        $payment->setQrLink('some-uri');
        $this->assertEquals('some-uri', $payment->getQrLink());

        $this->assertNull($payment->getRefundLink());
        $payment->setRefundLink('some-uri');
        $this->assertEquals('some-uri', $payment->getRefundLink());

        $this->assertNull($payment->getCheckoutLink());
        $payment->setCheckoutLink('some-uri');
        $this->assertEquals('some-uri',$payment->getCheckoutLink());

        $this->assertNull($payment->getReference());
        $payment->setReference('reference');
        $this->assertEquals('reference',$payment->getReference());

        $this->assertMatchesJsonSnapshot($payment->toArray());
    }
}
