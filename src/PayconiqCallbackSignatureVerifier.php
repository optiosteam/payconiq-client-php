<?php

declare(strict_types=1);

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
use phpseclib3\Crypt\EC\Formats\Signature\ASN1 as EcdsaAsn1;
use phpseclib3\Crypt\EC\Formats\Signature\IEEE as EcdsaP1363;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PayconiqCallbackSignatureVerifier
{
    // Legacy endpoints
    public const  CERTIFICATES_PRODUCTION_URL_LEGACY = 'https://payconiq.com/certificates';
    public const  CERTIFICATES_STAGING_URL_LEGACY = 'https://ext.payconiq.com/certificates';

    // New endpoints
    public const  CERTIFICATES_PRODUCTION_URL_NEW = 'https://jwks.bancontact.net';
    public const  CERTIFICATES_STAGING_URL_NEW = 'https://jwks.preprod.bancontact.net';

    private const TIMEOUT = 10;
    private const CONNECT_TIMEOUT = 2;

    private ClientInterface $httpClient;
    private AdapterInterface $cache;
    private bool $useProd;
    private bool $useNewPreProductionEnv; // only used for the new pre-production testing
    private JWSLoader $jwsLoader;

    public function __construct(
        string $paymentProfileId,
        ClientInterface $httpClient = null,
        AdapterInterface $cache = null,
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
            ]);
        }

        if (null === $cache) {
            $cache = new FilesystemAdapter();
        }

        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->useProd = $useProd;
        $this->useNewPreProductionEnv = $useNewPreProductionEnv;

        $this->jwsLoader = $this->initializeJwsLoader($paymentProfileId);
    }

    private function getCertificatesUrl(): string
    {
        if (true === $this->useNewPreProductionEnv || true === MigrationHelper::switchToNewEndpoints()) {
            // new endpoints
            return ($this->useProd ? self::CERTIFICATES_PRODUCTION_URL_NEW : self::CERTIFICATES_STAGING_URL_NEW);
        }

        // legacy endpoints
        return ($this->useProd ? self::CERTIFICATES_PRODUCTION_URL_LEGACY : self::CERTIFICATES_STAGING_URL_LEGACY);
    }

    public function isValid(string $token, ?string $payload = null, ?int $signature = 0): bool
    {
        try {
            $token = self::normalizeEcdsaSigIfNeeded($token, 32);
            $this->jwsLoader->loadAndVerifyWithKeySet($token, $this->getJWKSet(), $signature, $payload);
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws PayconiqCallbackSignatureVerificationException
     */
    public function loadAndVerifyJWS(string $token, ?string $payload = null, ?int $signature = 0): JWS
    {
        try {
            $token = self::normalizeEcdsaSigIfNeeded($token, 32);
            return $this->jwsLoader->loadAndVerifyWithKeySet($token, $this->getJWKSet(), $signature, $payload);
        } catch (\Throwable $e) {
            throw new PayconiqCallbackSignatureVerificationException(
                $this->useProd,
                sprintf('Something went wrong while loading and verifying the JWS. Error: %s', $e->getMessage()),
                $e->getCode(),
                $e,
            );
        }
    }

    /**
     * @throws PayconiqJWKSetException
     */
    private function getJWKSet(): JWKSet
    {
        try {
            $url = $this->getCertificatesUrl();
            $cacheKey = 'payconiq_certificates_' . md5($url);

            $JWKSetJson = $this->cache->get(
                key: $cacheKey,
                callback: function (ItemInterface $item) use ($url) {
                    $item->expiresAfter(CarbonInterval::hour(12));

                    $response = $this->httpClient->get($url);

                    return $response->getBody()->getContents();
                },
            );

            return JWKSet::createFromJson($JWKSetJson);
        } catch (\Throwable $e) {
            throw new PayconiqJWKSetException(
                $this->useProd,
                sprintf('Something went wrong while fetching the JWK Set. Error: %s', $e->getMessage()),
                $e->getCode(),
                $e,
            );
        }
    }

    /**
     * If the compact JWS uses a DER-encoded ECDSA signature, convert it to JOSE raw (r||s).
     * $partLen: 32 for ES256, 48 for ES384, 66 for ES512.
     */
    private static function normalizeEcdsaSigIfNeeded(string $compactJws, int $partLen = 32): string
    {
        [$h, $p, $sB64u] = explode('.', $compactJws, 3) + [null, null, null];
        if ($sB64u === null || $sB64u === '') return $compactJws;

        // base64url decode signature
        $pad = (4 - strlen($sB64u) % 4) % 4;
        $sig = base64_decode(strtr($sB64u, '-_', '+/') . str_repeat('=', $pad), true);
        if ($sig === false) return $compactJws;

        // already raw r||s of expected length? nothing to do.
        if (strlen($sig) === 2 * $partLen) return $compactJws;

        // Try DER → raw using phpseclib helpers
        try {
            $rs  = EcdsaAsn1::load($sig);                              // ['r'=>BigInteger,'s'=>BigInteger]
            $raw = EcdsaP1363::save($rs['r'], $rs['s'], null, $partLen); // fixed-length P-1363
            $sB64u = rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
            return "$h.$p.$sB64u";
        } catch (\Throwable) {
            // Not DER / not parseable — leave as-is and let the verifier decide
            return $compactJws;
        }
    }

    private function initializeJwsLoader(string $paymentProfileId): JWSLoader
    {
        return new JWSLoader(
            new JWSSerializerManager([
                new CompactSerializer(),
            ]),
            new JWSVerifier(
                new AlgorithmManager([
                    new ES256(),
                ]),
            ),
            new HeaderCheckerManager(
                [
                    new AlgorithmChecker(['ES256']),
                    new PayconiqSubChecker($paymentProfileId),
                    new PayconiqIssChecker(),
                    new PayconiqIssuedAtChecker(),
                    new PayconiqJtiChecker(),
                    new PayconiqPathChecker(),
                ],
                [
                    new JWSTokenSupport(),
                ],
            ),
        );
    }
}
