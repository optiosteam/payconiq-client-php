<?php

namespace Tests\Optios\Payconiq\Resource\Payment;

use Carbon\Carbon;
use Optios\Payconiq\Enum\PaymentStatus;
use Optios\Payconiq\Resource\Payment\Payment;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class PaymentTest extends TestCase
{
    use MatchesSnapshots;

    public function testPayment(): void {
        $obj = new \stdClass();
        $obj->paymentId = 'payment-id';
        $obj->createdAt = '2022-01-25';
        $obj->status = 'SUCCEEDED';
        $obj->amount = 10;
        $obj->creditor = new \stdClass();
        $obj->creditor->profileId = 'profile-id';
        $obj->creditor->merchantId = 'merchant-id';
        $obj->creditor->name = 'name';
        $obj->creditor->iban = 'iban';
        $obj->debtor = new \stdClass();

        $payment = Payment::createFromObject($obj);

        $this->assertEquals('payment-id', $payment->getPaymentId());
        $this->assertEquals(new Carbon('2022-01-25'), $payment->getCreatedAt());
        $this->assertNull($payment->getExpiresAt());
        $this->assertEquals('EUR', $payment->getCurrency());
        $this->assertEquals(PaymentStatus::SUCCEEDED, $payment->getStatus());
        $this->assertEquals(
            [
                'profileId' => 'profile-id',
                'merchantId' => 'merchant-id',
                'name' => 'name',
                'iban' => 'iban',
                'callbackUrl' => null,
            ],
            $payment->getCreditor()->toArray(),
        );
        $this->assertEquals(
            [
                'name' => null,
                'iban' => null,
            ],
            $payment->getDebtor()->toArray(),
        );
        $this->assertEquals(10, $payment->getAmount());
        $this->assertNull($payment->getTransferAmount());
        $this->assertNull($payment->getTippingAmount());
        $this->assertNull($payment->getTotalAmount());
        $this->assertNull($payment->getDescription());
        $this->assertNull($payment->getBulkId());
        $this->assertNull($payment->getSelfLink());
        $this->assertNull($payment->getDeepLink());
        $this->assertNull($payment->getQrLink());
        $this->assertNull($payment->getRefundLink());
        $this->assertNull($payment->getCheckoutLink());
        $this->assertNull($payment->getReference());

        $this->assertMatchesJsonSnapshot($payment->toArray());
    }
}
