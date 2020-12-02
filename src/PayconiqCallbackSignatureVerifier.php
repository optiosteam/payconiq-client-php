<?php
declare(strict_types = 1);

namespace Optios\Payconiq;

use Carbon\CarbonInterval;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Jose\Component\Checker\AlgorithmChecker;
use Jose\Component\Checker\HeaderCheckerManager;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSLoader;
use Jose\Component\Signature\JWSTokenSupport;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Optios\Payconiq\Exception\PayconiqCallbackSignatureVerificationException;
use Optios\Payconiq\Exception\PayconiqJWKSetException;
use Optios\Payconiq\HeaderChecker\PayconiqIssChecker;
use Optios\Payconiq\HeaderChecker\PayconiqIssuedAtChecker;
use Optios\Payconiq\HeaderChecker\PayconiqJtiChecker;
use Optios\Payconiq\HeaderChecker\PayconiqPathChecker;
use Optios\Payconiq\HeaderChecker\PayconiqSubChecker;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class PayconiqCallbackSignatureVerifier
 * @package Optios\Payconiq
 */
class PayconiqCallbackSignatureVerifier
{
    public const CERTIFICATES_URL     = 'https://payconiq.com/certificates';
    public const CERTIFICATES_EXT_URL = 'https://ext.payconiq.com/certificates';
    private const TIMEOUT              = 10;
    private const CONNECT_TIMEOUT      = 2;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @var bool
     */
    private $useProd;

    /**
     * @var JWSLoader
     */
    private $jwsLoader;

    /**
     * PayconiqCallbackSignatureVerifier constructor.
     *
     * @param string                $merchantProfileId
     * @param ClientInterface|null  $httpClient
     * @param AdapterInterface|null $cache
     * @param bool                  $useProd
     */
    public function __construct(
        string $merchantProfileId,
        ClientInterface $httpClient = null,
        AdapterInterface $cache = null,
        bool $useProd = true
    ) {
        if (null === $httpClient) {
            $httpClient = new Client([
                RequestOptions::TIMEOUT => self::TIMEOUT,
                RequestOptions::CONNECT_TIMEOUT => self::CONNECT_TIMEOUT,
            ]);
        }

        if (null === $cache) {
            $cache = new FilesystemAdapter();
        }

        $this->httpClient = $httpClient;
        $this->cache      = $cache;
        $this->useProd    = $useProd;

        $this->jwsLoader = $this->initializeJwsLoader($merchantProfileId);
    }

    /**
     * @param string      $token
     * @param string|null $payload
     * @param int|null    $signature
     *
     * @return bool
     */
    public function isValid(string $token, ?string $payload = null, ?int $signature = 0): bool
    {
        try {
            $this->jwsLoader->loadAndVerifyWithKeySet($token, $this->getJWKSet(), $signature, $payload);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param string      $token
     * @param string|null $payload
     * @param int|null    $signature
     *
     * @return JWS
     * @throws PayconiqCallbackSignatureVerificationException
     */
    public function loadAndVerifyJWS(string $token, ?string $payload = null, ?int $signature = 0): JWS
    {
        try {
            return $this->jwsLoader->loadAndVerifyWithKeySet($token, $this->getJWKSet(), $signature, $payload);
        } catch (\Throwable $e) {
            throw new PayconiqCallbackSignatureVerificationException(
                $this->useProd,
                sprintf('Something went wrong while loading and verifying the JWS. Error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @return JWKSet
     * @throws PayconiqJWKSetException
     */
    private function getJWKSet(): JWKSet
    {
        try {
            $url      = $this->useProd ? self::CERTIFICATES_URL : self::CERTIFICATES_EXT_URL;
            $cacheKey = 'payconiq_certificates_' . ($this->useProd ? 'prod' : 'ext');

            $JWKSetJson = $this->cache->get($cacheKey,
                function(ItemInterface $item) use ($url) {
                    $item->expiresAfter(CarbonInterval::hour(12));

                    $response = $this->httpClient->get($url);

                    return $response->getBody()->getContents();
                }
            );

            return JWKSet::createFromJson($JWKSetJson);
        } catch (\Throwable $e) {
            throw new PayconiqJWKSetException(
                $this->useProd,
                sprintf('Something went wrong while fetching the JWK Set. Error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $merchantProfileId
     *
     * @return JWSLoader
     */
    private function initializeJwsLoader(string $merchantProfileId): JWSLoader
    {
        return new JWSLoader(
            new JWSSerializerManager([
                new CompactSerializer(),
            ]),
            new JWSVerifier(
                new AlgorithmManager([
                    new ES256(),
                ])
            ),
            new HeaderCheckerManager(
                [
                    new AlgorithmChecker(['ES256']),
                    new PayconiqSubChecker($merchantProfileId),
                    new PayconiqIssChecker(),
                    new PayconiqIssuedAtChecker(),
                    new PayconiqJtiChecker(),
                    new PayconiqPathChecker(),
                ],
                [
                    new JWSTokenSupport(),
                ]
            )
        );
    }
}
