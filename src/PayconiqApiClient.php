<?php

declare(strict_types=1);

namespace Optios\Payconiq;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use League\Uri\Http;
use League\Uri\Modifier;
use Optios\Payconiq\Exception\PayconiqApiException;
use Optios\Payconiq\Request\RequestPayment;
use Optios\Payconiq\Request\SearchPayments;
use Optios\Payconiq\Resource\Payment\Payment;
use Optios\Payconiq\Resource\Search\SearchResult;

class PayconiqApiClient
{
    public const  API_VERSION = 'v3';

    // Legacy endpoints
    public const  API_ENDPOINT_PRODUCTION_LEGACY = 'https://api.payconiq.com/';
    public const  API_ENDPOINT_STAGING_LEGACY = 'https://api.ext.payconiq.com/';

    // New endpoints
    public const  API_ENDPOINT_PRODUCTION_NEW = 'https://merchant.api.bancontact.net/';
    public const  API_ENDPOINT_STAGING_NEW = 'https://merchant.api.preprod.bancontact.net/';

    private const TIMEOUT = 10;
    private const CONNECT_TIMEOUT = 2;

    private string $apiKey;
    private ClientInterface $httpClient;
    private bool $useProd;
    private bool $useNewPreProductionEnv; // only used for the new pre-production testing

    public function __construct(
        string $apiKey,
        ?ClientInterface $httpClient = null,
        bool $useProd = true,
        bool $useNewPreProductionEnv = false,
    ) {
        if (
            true === $useProd
            && true === $useNewPreProductionEnv
            && false === MigrationHelper::switchToNewEndpoints()
        ) {
            throw new \InvalidArgumentException('You can not use the new pre production env in production mode yet');
        }

        if (null === $httpClient) {
            $httpClient = new Client([
                RequestOptions::TIMEOUT => self::TIMEOUT,
                RequestOptions::CONNECT_TIMEOUT => self::CONNECT_TIMEOUT,
                RequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
            ]);
        }

        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
        $this->useProd = $useProd;
        $this->useNewPreProductionEnv = $useNewPreProductionEnv;
    }

    public function getApiEndpointBase(): string
    {
        return $this->getEndpoint() . self::API_VERSION;
    }

    private function getEndpoint(): string
    {
        if (true === $this->useNewPreProductionEnv || true === MigrationHelper::switchToNewEndpoints()) {
            // new endpoints
            return ($this->useProd ? self::API_ENDPOINT_PRODUCTION_NEW : self::API_ENDPOINT_STAGING_NEW);
        }

        // legacy endpoints
        return ($this->useProd ? self::API_ENDPOINT_PRODUCTION_LEGACY : self::API_ENDPOINT_STAGING_LEGACY);
    }

    /**
     * @throws PayconiqApiException
     */
    public function requestPayment(RequestPayment $requestPayment): Payment
    {
        try {
            $uri = $this->getApiEndpointBase() . '/payments' . ($requestPayment->getPosId() ? '/pos' : null);
            $response = $this->httpClient->post(
                uri: $uri,
                options: [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    RequestOptions::JSON => $requestPayment->toArray(),
                ],
            );

            return Payment::createFromResponse($response);
        } catch (ClientException $e) {
            throw $this->convertToPayconiqApiException($e);
        }
    }

    /**
     * @throws PayconiqApiException
     */
    public function getPayment(string $paymentId): Payment
    {
        try {
            $response = $this->httpClient->get(
                uri: $this->getApiEndpointBase() . '/payments/' . $paymentId,
                options: [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                ],
            );

            return Payment::createFromResponse($response);
        } catch (ClientException $e) {
            throw $this->convertToPayconiqApiException($e);
        }
    }

    /**
     * @throws PayconiqApiException
     */
    public function cancelPayment(string $paymentId): bool
    {
        try {
            $this->httpClient->delete(
                uri: $this->getApiEndpointBase() . '/payments/' . $paymentId,
                options: [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                ],
            );
        } catch (ClientException $e) {
            throw $this->convertToPayconiqApiException($e);
        }

        return true;
    }

    /**
     * @throws PayconiqApiException
     */
    public function searchPayments(
        SearchPayments $search,
        int $page = 0,
        int $size = 50,
    ): SearchResult {
        try {
            $uri = Modifier::from(
                Http::new($this->getApiEndpointBase() . '/payments/search'),
            )
                ->mergeQueryParameters([
                    'page' => $page,
                    'size' => $size,
                ])
                ->getUri();

            $response = $this->httpClient->post(
                uri: (string) $uri,
                options: [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    RequestOptions::JSON => $search->toArray(),
                ],
            );

            return SearchResult::createFromResponse($response);
        } catch (ClientException $e) {
            throw $this->convertToPayconiqApiException($e);
        }
    }

    /**
     * @throws PayconiqApiException
     */
    public function refundPayment(string $paymentId)
    {
        try {
            $this->httpClient->get(
                uri: $this->getApiEndpointBase() . '/payments/' . $paymentId . '/debtor/refundIban',
            );
        } catch (ClientException $e) {
            throw $this->convertToPayconiqApiException($e);
        }

        return true;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    private function convertToPayconiqApiException(ClientException $e): PayconiqApiException
    {
        $contents = $e->getResponse()->getBody()->getContents() ?? null;
        if (empty($contents)) {
            return new PayconiqApiException(
                payconiqMessage: null,
                payconiqCode: null,
                traceId: null,
                spanId: null,
                isProd: $this->useProd,
                message: $e->getMessage(),
                code: $e->getCode(),
            );
        }

        $message = json_decode($contents);

        return new PayconiqApiException(
            payconiqMessage: $message->message ?? null,
            payconiqCode: $message->code ?? null,
            traceId: $message->traceId ?? null,
            spanId: $message->spanId ?? null,
            isProd: $this->useProd,
            message: $e->getMessage(),
            code: $e->getCode(),
        );
    }
}
