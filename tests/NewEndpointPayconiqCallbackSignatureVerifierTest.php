<?php

namespace Tests\Optios\Payconiq;

use Carbon\CarbonImmutable;
use GuzzleHttp\Client;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSLoader;
use Optios\Payconiq\Exception\PayconiqCallbackSignatureVerificationException;
use Optios\Payconiq\MigrationHelper;
use Optios\Payconiq\PayconiqCallbackSignatureVerifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class NewEndpointPayconiqCallbackSignatureVerifierTest extends TestCase
{
    private $payconiqCallbackSignatureVerifier;
    private $paymentProfileId;
    private $httpClient;
    private $cache;
    private $useProd;
    private $jwsLoader;

    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                MigrationHelper::SWITCH_DATETIME,
                MigrationHelper::TIMEZONE,
            ),
        );

        $this->paymentProfileId = 'profileId';
        $this->httpClient = $this->createMock(Client::class);
        $this->cache = $this->createMock(FilesystemAdapter::class);
        $this->useProd = false;

        $this->payconiqCallbackSignatureVerifier = new PayconiqCallbackSignatureVerifier(
            $this->paymentProfileId,
            $this->httpClient,
            $this->cache,
            $this->useProd,
        );

        $this->jwsLoader = $this->createMock(JWSLoader::class);

        // Because the jwsLoader is not injected in to the verifier,
        // we need to do some magic to make sure we can mock it.
        // Ideally this should be refactored to DI.
        $class = new \ReflectionClass(PayconiqCallbackSignatureVerifier::class);
        $property = $class->getProperty('jwsLoader');
        $property->setAccessible(true);
        $property->setValue($this->payconiqCallbackSignatureVerifier, $this->jwsLoader);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
    }

    public function testIsValid(): void
    {
        $url = 'https://jwks.preprod.bancontact.net';
        $cacheKey = 'payconiq_certificates_' . md5($url);
        $jwkSetJson = json_encode(['keys' => [['kty' => 'string']]]);
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with($cacheKey, function (ItemInterface $item) use ($url) {
            })
            ->willReturn($jwkSetJson);

        $this->jwsLoader
            ->expects($this->once())
            ->method('loadAndVerifyWithKeySet')
            ->with('some-token', JWKSet::createFromJson($jwkSetJson), 0, null);

        $this->assertTrue($this->payconiqCallbackSignatureVerifier->isValid('some-token'));
    }

    public function testIsInvalid(): void
    {
        $url = 'https://jwks.preprod.bancontact.net';
        $cacheKey = 'payconiq_certificates_' . md5($url);
        $jwkSetJson = json_encode(['keys' => [['kty' => 'string']]]);
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with($cacheKey, function (ItemInterface $item) use ($url) {
            })
            ->willReturn($jwkSetJson);

        $this->jwsLoader
            ->expects($this->once())
            ->method('loadAndVerifyWithKeySet')
            ->with('some-token', JWKSet::createFromJson($jwkSetJson), 0, null)
            ->willThrowException(new \Exception('Unable to load and verify the token.'));

        $this->assertFalse($this->payconiqCallbackSignatureVerifier->isValid('some-token'));
    }

    public function testLoadAndVerifyJWS(): void
    {
        $url = 'https://jwks.preprod.bancontact.net';
        $cacheKey = 'payconiq_certificates_' . md5($url);
        $jwkSetJson = json_encode(['keys' => [['kty' => 'string']]]);
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with($cacheKey, function (ItemInterface $item) use ($url) {
            })
            ->willReturn($jwkSetJson);

        $this->jwsLoader
            ->expects($this->once())
            ->method('loadAndVerifyWithKeySet')
            ->with('some-token', JWKSet::createFromJson($jwkSetJson), 0, null)
            ->willReturn(new JWS('the-payload', 'encoded-payload'));

        $this->assertInstanceOf(
            JWS::class,
            $this->payconiqCallbackSignatureVerifier->loadAndVerifyJWS('some-token'),
        );
    }

    public function testLoadAndVerifyJWSItShouldThrow(): void
    {
        $url = 'https://jwks.preprod.bancontact.net';
        $cacheKey = 'payconiq_certificates_' . md5($url);
        $jwkSetJson = json_encode(['keys' => [['kty' => 'string']]]);
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with($cacheKey, function (ItemInterface $item) use ($url) {
            })
            ->willReturn($jwkSetJson);

        $this->jwsLoader
            ->expects($this->once())
            ->method('loadAndVerifyWithKeySet')
            ->with('some-token', JWKSet::createFromJson($jwkSetJson), 0, null)
            ->willThrowException(new \Exception('Unable to load and verify the token.'));

        $this->expectException(PayconiqCallbackSignatureVerificationException::class);
        //phpcs:disable
        $this->expectExceptionMessage(
            'Something went wrong while loading and verifying the JWS. Error: Unable to load and verify the token.',
        );
        //phpcs:enable

        $this->payconiqCallbackSignatureVerifier->loadAndVerifyJWS('some-token');
    }
}
