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
//    const ALG                  = 'ES256'; //todo: remove?
    const CERTIFICATES_URL     = 'https://payconiq.com/certificates';
    const CERTIFICATES_EXT_URL = 'https://ext.payconiq.com/certificates';
//    const KID_URL              = 'es.signature.payconiq.com';     //todo: figure out if this is still needed since we use JWK set??
//    const KID_EXT_URL          = 'es.signature.ext.payconiq.com'; //todo: figure out if this is still needed since we use JWK set??
    const TIMEOUT         = 10;
    const CONNECT_TIMEOUT = 2;

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
     * @param int         $signature
     * @param string|null $payload
     *
     * @return bool
     * @throws PayconiqCallbackSignatureVerificationException
     */
    public function isValid(string $token, int $signature, ?string $payload = null): bool
    {
        try {
            $this->jwsLoader->loadAndVerifyWithKeySet($token, $this->getJWKSet(), $signature, $payload);

            return true;
        } catch (\Throwable $e) {
            return false;
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
    private function initializeJwsLoader(string $merchantProfileId)
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
