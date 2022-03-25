<?php

namespace Tests\Optios\Payconiq;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Optios\Payconiq\Exception\PayconiqApiException;
use Optios\Payconiq\PayconiqApiClient;
use Optios\Payconiq\Request\RequestPayment;
use Optios\Payconiq\Request\SearchPayments;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class PayconiqApiClientTest extends TestCase
{
    use MatchesSnapshots;

    private $payconiqApiClient;
    private $apiKey;
    private $httpClient;
    private $useProd;

    public function testSetApiKey(): void
    {
        $this->payconiqApiClient->setApiKey('new-api-key');
        $this->assertEquals('new-api-key', $this->payconiqApiClient->getApiKey());
    }

    public function testRequestPayment(): void
    {
        $requestPayment = RequestPayment::createForStaticQR(10, 'pos-id');

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with(
                'https://api.ext.payconiq.com/v3/payments/pos',
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    RequestOptions::JSON => $requestPayment->toArray(),
                ]
            )
            ->willReturnCallback(function ($uri, array $options) {
                $this->assertEquals('https://api.ext.payconiq.com/v3/payments/pos', $uri);
                $this->assertMatchesJsonSnapshot($options);

                return new Response(200, [], json_encode([
                    'paymentId' => 'payment-id',
                    'createdAt' => '2022-01-25',
                    'status' => 'active',
                    'amount' => 10,
                ]));
            });

        $this->payconiqApiClient->requestPayment($requestPayment);
    }

    public function testRequestPaymentItShouldThrow(): void
    {
        $requestPayment = RequestPayment::createForStaticQR(10, 'pos-id');

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with(
                'https://api.ext.payconiq.com/v3/payments/pos',
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    RequestOptions::JSON => $requestPayment->toArray(),
                ]
            )
            ->willThrowException(new ClientException(
                'some-message',
                $this->createMock(Request::class),
                $this->createMock(Response::class)
            ));

        $this->expectException(PayconiqApiException::class);
        $this->expectExceptionMessage('some-message');
        $this->payconiqApiClient->requestPayment($requestPayment);
    }

    public function testGetPayment(): void
    {
        $paymentId = 'payment-id';
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with(
                'https://api.ext.payconiq.com/v3/payments/' . $paymentId,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                ]
            )
            ->willReturnCallback(function ($uri, array $options) {
                $this->assertEquals('https://api.ext.payconiq.com/v3/payments/payment-id', $uri);
                $this->assertMatchesJsonSnapshot($options);

                return new Response(200, [], json_encode([
                    'paymentId' => 'payment-id',
                    'createdAt' => '2022-01-25',
                    'status' => 'active',
                    'amount' => 10,
                ]));
            });

        $this->payconiqApiClient->getPayment($paymentId);
    }

    public function testGetPaymentItShouldThrow(): void
    {
        $paymentId = 'payment-id';
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with(
                'https://api.ext.payconiq.com/v3/payments/' . $paymentId,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                ]
            )
            ->willThrowException(new ClientException(
                'some-message',
                $this->createMock(Request::class),
                $this->createMock(Response::class)
            ));

        $this->expectException(PayconiqApiException::class);
        $this->expectExceptionMessage('some-message');
        $this->payconiqApiClient->getPayment($paymentId);
    }

    public function testCancelPayment(): void
    {
        $paymentId = 'payment-id';
        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with(
                'https://api.ext.payconiq.com/v3/payments/' . $paymentId,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                ]
            );

        $this->payconiqApiClient->cancelPayment($paymentId);
    }

    public function testCancelPaymentItShouldThrow(): void
    {
        $paymentId = 'payment-id';
        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with(
                'https://api.ext.payconiq.com/v3/payments/' . $paymentId,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                ]
            )
            ->willThrowException(new ClientException(
                'some-message',
                $this->createMock(Request::class),
                $this->createMock(Response::class)
            ));

        $this->expectException(PayconiqApiException::class);
        $this->expectExceptionMessage('some-message');

        $this->payconiqApiClient->cancelPayment($paymentId);
    }

    public function testSearchPayments(): void
    {
        $searchPayments = new SearchPayments(new \DateTime('2022-01-25'));

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with(
                'https://api.ext.payconiq.com/v3/payments/search?page=0&size=100',
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    RequestOptions::JSON => $searchPayments->toArray(),
                ]
            )
            ->willReturnCallback(function ($uri, array $options) {
                $this->assertEquals('https://api.ext.payconiq.com/v3/payments/search?page=0&size=100', $uri);
                $this->assertMatchesJsonSnapshot($options);

                return new Response(200, [], json_encode([
                    'size' => 100,
                    'totalPages' => 20,
                    'totalElements' => 200,
                    'number' => 1,
                ]));
            });

        $this->payconiqApiClient->searchPayments($searchPayments, 0, 100);
    }

    public function testSearchPaymentsItShouldThrow(): void
    {
        $searchPayments = new SearchPayments(new \DateTime('2022-01-25'));

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with(
                'https://api.ext.payconiq.com/v3/payments/search?page=0&size=100',
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    RequestOptions::JSON => $searchPayments->toArray(),
                ]
            )
            ->willThrowException(new ClientException(
                'some-message',
                $this->createMock(Request::class),
                $this->createMock(Response::class)
            ));

        $this->expectException(PayconiqApiException::class);
        $this->expectExceptionMessage('some-message');

        $this->payconiqApiClient->searchPayments($searchPayments, 0, 100);
    }

    public function testRefundPayment(): void
    {
        $paymentId = 'payment-id';
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('https://api.ext.payconiq.com/v3/payments/' . $paymentId . '/debtor/refundIban');

        $this->payconiqApiClient->refundPayment($paymentId);
    }

    public function testRefundPaymentItShouldThrow(): void
    {
        $paymentId = 'payment-id';
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with(
                'https://api.ext.payconiq.com/v3/payments/' . $paymentId . '/debtor/refundIban'
            )
            ->willThrowException(new ClientException(
                'some-message',
                $this->createMock(Request::class),
                $this->createMock(Response::class)
            ));

        $this->expectException(PayconiqApiException::class);
        $this->expectExceptionMessage('some-message');

        $this->payconiqApiClient->refundPayment($paymentId);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiKey     = 'some-api-key';
        $this->httpClient = $this->createMock(Client::class);
        $this->useProd    = false;

        $this->payconiqApiClient = new PayconiqApiClient(
            $this->apiKey,
            $this->httpClient,
            $this->useProd
        );
    }
}
