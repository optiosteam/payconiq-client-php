<?php

namespace Tests\Optios\Payconiq\Request;

use Optios\Payconiq\Request\RequestPayment;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class RequestPaymentTest extends TestCase
{
    use MatchesSnapshots;

    public function testRequestPayment(): void
    {
        $requestPayment = RequestPayment::createForStaticQR(1, 'posId');

        $this->assertEquals(1, $requestPayment->getAmount());
        $requestPayment->setAmount(2);
        $this->assertEquals(2, $requestPayment->getAmount());

        $this->assertEquals('EUR', $requestPayment->getCurrency());
        $requestPayment->setCurrency('USD');
        $this->assertEquals('USD', $requestPayment->getCurrency());

        $this->assertNull($requestPayment->getCallbackUrl());
        $requestPayment->setCallbackUrl('some-uri');
        $this->assertEquals('some-uri', $requestPayment->getCallbackUrl());

        $this->assertNull($requestPayment->getReference());
        $requestPayment->setReference('ref');
        $this->assertEquals('ref', $requestPayment->getReference());

        $this->assertNull($requestPayment->getDescription());
        $requestPayment->setDescription('description');
        $this->assertEquals('description', $requestPayment->getDescription());

        $this->assertNull($requestPayment->getBulkId());
        $requestPayment->setBulkId('bulk-id');
        $this->assertEquals('bulk-id', $requestPayment->getBulkId());

        $this->assertNull($requestPayment->getShopId());
        $requestPayment->setShopId('shop-id');
        $this->assertEquals('shop-id', $requestPayment->getShopId());

        $this->assertNull($requestPayment->getShopName());
        $requestPayment->setShopName('shop-name');
        $this->assertEquals('shop-name', $requestPayment->getShopName());

        $this->assertNull($requestPayment->getReturnUrl());
        $requestPayment->setReturnUrl('some-uri');
        $this->assertEquals('some-uri', $requestPayment->getReturnUrl());

        $this->assertMatchesJsonSnapshot($requestPayment->toArray());
    }
}
