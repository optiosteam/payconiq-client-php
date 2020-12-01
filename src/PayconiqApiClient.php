<?php
declare(strict_types = 1);

namespace Optios\Payconiq;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Optios\Payconiq\Exception\PayconiqApiException;
use Optios\Payconiq\Request\CreatePayment;
use Optios\Payconiq\Resource\Payment\Payment;

/**
 * Class PayconiqApiClient
 * @package Optios\Payconiq
 */
class PayconiqApiClient
{
    const API_VERSION      = 'v3';
    const API_ENDPOINT     = 'https://api.payconiq.com/';
    const API_EXT_ENDPOINT = 'https://api.ext.payconiq.com/';
    const TIMEOUT          = 10;
    const CONNECT_TIMEOUT  = 2;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var bool
     */
    private $useProd;

    /**
     * PayconiqApiClient constructor.
     *
     * @param string               $apiKey
     * @param ClientInterface|null $httpClient
     * @param bool                 $useProd
     */
    public function __construct(string $apiKey, ClientInterface $httpClient = null, bool $useProd = true)
    {
        if (null === $httpClient) {
            $httpClient = new Client([
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                RequestOptions::TIMEOUT => self::TIMEOUT,
                RequestOptions::CONNECT_TIMEOUT => self::CONNECT_TIMEOUT,
                RequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
            ]);
        }

        $this->httpClient = $httpClient;
        $this->useProd    = $useProd;
    }

    public function createPayment(CreatePayment $createPayment): Payment
    {
        try {
            $response = $this->httpClient->post(
                $this->getApiEndpointBase() . '/payments',
                [
                    RequestOptions::JSON => $createPayment->toArray(),

                ]
            );

            return Payment::createFromResponse($response);
        } catch (ClientException $e) {
            throw $this->convertToPayconiqApiException($e);
        }
    }

    public function getPayment(string $paymentId): Payment
    {
        //todo figure out exception handling
        $response = $this->httpClient->get(
            $this->getApiEndpointBase() . '/payments/' / $paymentId
        );

        return Payment::createFromResponse($response);
    }

    public function cancelPayment(string $paymentId)
    {
        $this->httpClient->delete(
            $this->getApiEndpointBase() . '/payments/' . $paymentId
        );
    }

    public function searchPayments()
    {
        //todo
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getApiEndpointBase(): string
    {
        return ($this->useProd ? self::API_ENDPOINT : self::API_EXT_ENDPOINT) . self::API_VERSION;
    }

    private function convertToPayconiqApiException(ClientException $e)
    {
        $message = json_decode(
            $e->getResponse()->getBody()->getContents()
        );

        return new PayconiqApiException(
            $message->message ?? null,
            $message->code ?? null,
            $message->traceId ?? null,
            $message->spanId ?? null,
            $this->useProd,
            $e->getMessage(),
            $e->getCode()
        );
    }
}
