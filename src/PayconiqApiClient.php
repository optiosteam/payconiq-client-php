<?php
declare(strict_types = 1);

namespace Optios\Payconiq;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use League\Url\Url;
use Optios\Payconiq\Exception\PayconiqApiException;
use Optios\Payconiq\Request\RequestPayment;
use Optios\Payconiq\Request\SearchPayments;
use Optios\Payconiq\Resource\Payment\Payment;
use Optios\Payconiq\Resource\Search\SearchResult;

/**
 * Class PayconiqApiClient
 * @package Optios\Payconiq
 */
class PayconiqApiClient
{
    public const  API_VERSION      = 'v3';
    public const  API_ENDPOINT     = 'https://api.payconiq.com/';
    public const  API_EXT_ENDPOINT = 'https://api.ext.payconiq.com/';
    private const TIMEOUT          = 10;
    private const CONNECT_TIMEOUT  = 2;

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
                RequestOptions::TIMEOUT => self::TIMEOUT,
                RequestOptions::CONNECT_TIMEOUT => self::CONNECT_TIMEOUT,
                RequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
            ]);
        }

        $this->apiKey     = $apiKey;
        $this->httpClient = $httpClient;
        $this->useProd    = $useProd;
    }

    /**
     * @param RequestPayment $requestPayment
     *
     * @return Payment
     * @throws PayconiqApiException
     */
    public function requestPayment(RequestPayment $requestPayment): Payment
    {
        try {
            $uri      = $this->getApiEndpointBase() . '/payments' . ($requestPayment->getPosId() ? '/pos' : null);
            $response = $this->httpClient->post(
                $uri,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    RequestOptions::JSON => $requestPayment->toArray(),
                ]
            );

            return Payment::createFromResponse($response);
        } catch (ClientException | GuzzleException $e) {
            throw $this->convertToPayconiqApiException($e);
        }
    }

    /**
     * @param string $paymentId
     *
     * @return Payment
     * @throws PayconiqApiException
     */
    public function getPayment(string $paymentId): Payment
    {
        try {
            $response = $this->httpClient->get(
                $this->getApiEndpointBase() . '/payments/' . $paymentId,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                ]
            );

            return Payment::createFromResponse($response);
        } catch (ClientException | GuzzleException $e) {
            throw $this->convertToPayconiqApiException($e);
        }
    }

    /**
     * @param string $paymentId
     *
     * @return bool
     * @throws PayconiqApiException
     */
    public function cancelPayment(string $paymentId): bool
    {
        try {
            $this->httpClient->delete(
                $this->getApiEndpointBase() . '/payments/' . $paymentId,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                ]
            );
        } catch (ClientException | GuzzleException $e) {
            throw $this->convertToPayconiqApiException($e);
        }

        return true;
    }

    /**
     * @param SearchPayments $search
     * @param int            $page
     * @param int            $size
     *
     * @return SearchResult
     * @throws PayconiqApiException
     */
    public function searchPayments(
        SearchPayments $search,
        int $page = 0,
        int $size = 50
    ): SearchResult {
        try {
            $url = Url::createFromUrl($this->getApiEndpointBase() . '/payments/search');
            $url->getQuery()->modify(['page' => $page, 'size' => $size]);

            $response = $this->httpClient->post(
                $url->__toString(),
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    RequestOptions::JSON => $search->toArray(),
                ]
            );

            return SearchResult::createFromResponse($response);
        } catch (ClientException | GuzzleException $e) {
            throw $this->convertToPayconiqApiException($e);
        }
    }

    /**
     * @param string $paymentId
     *
     * @return bool
     * @throws PayconiqApiException
     */
    public function refundPayment(string $paymentId)
    {
        try {
            $this->httpClient->get(
                $this->getApiEndpointBase() . '/payments/' . $paymentId . '/debtor/refundIban'
            );
        } catch (ClientException | GuzzleException $e) {
            throw $this->convertToPayconiqApiException($e);
        }

        return true;
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

    /**
     * @return string
     */
    public function getApiEndpointBase(): string
    {
        return ($this->useProd ? self::API_ENDPOINT : self::API_EXT_ENDPOINT) . self::API_VERSION;
    }

    /**
     * @param \Throwable $e
     *
     * @return PayconiqApiException
     */
    private function convertToPayconiqApiException(\Throwable $e)
    {
        $contents = $e->getResponse()->getBody()->getContents() ?? null;
        if (empty($contents)) {
            return new PayconiqApiException(
                null,
                null,
                null,
                null,
                $this->useProd,
                $e->getMessage(),
                $e->getCode()
            );
        }

        $message = json_decode($contents);

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
