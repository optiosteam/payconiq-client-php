[![CI](https://github.com/optiosteam/payconiq-client-php/actions/workflows/tests.yaml/badge.svg?branch=main)](https://github.com/optiosteam/payconiq-client-php/actions/workflows/tests.yaml)
[![codecov](https://codecov.io/gh/robiningelbrecht/payconiq-client-php/branch/ci-and-more-unit-tests/graph/badge.svg?token=0B5HFVIJBY)](https://codecov.io/gh/robiningelbrecht/payconiq-client-php)

# PHP Payconiq API Client (unofficial)

Supported API version: v3

Development sponsored by [Optios](https://www.optios.net)

API Documentation: https://developer.payconiq.com/online-payments-dock/#payment-api-version-3-v3-

## Supported API functions
This library provides support for the following Payconiq API (v3) functions:
- Payconiq Instore (V3) - Terminal & Display
- Payconiq Instore (V3) - Static QR Sticker
- Payconiq Instore (V3) - Receipt
- Payconiq Invoice (V3) - Invoice
- Payconiq Online (V3) - Custom Online
- Payconiq Online (V3) - Checkout Flow Online
- Payconiq Online (V3) - App2App Linking
- Payconiq Online (V3) - Top-up

Not supported yet:
- Loyalty Integration
- Payout Reconciliation API

## Installation

**Requirement**: PHP version >=7.2

```
composer require optiosteam/payconiq-client-php
```

## Description
This library provides 3 main classes:
- `PayconiqApiClient`
- `PayconiqCallbackSignatureVerifier`
- `PayconiqQrCodeGenerator`

### PayconiqApiClient
This is the main class for performing REST calls to the Payconiq API, e.g. create payments, cancel payments, search payments & refund payments.

In the constructor you have to pass your Payconiq API key, optionally you can also inject your own Guzzle Client and specify if you want to use the production environment of the Payconiq API or the testing (Ext) environment.
```php
public function __construct(string $apiKey, ClientInterface $httpClient = null, bool $useProd = true)
```

### PayconiqCallbackSignatureVerifier
This class is used for TLS Two-way TLS Encryption Support (TLS-Mutual Authentication). It verifies the callback body, JSON Web Signature (JWS) and the header fields in the JOSE header.

In the constructor you have to pass your Payconiq Payment Profile Id, optionally you can also inject your own Guzzle Client and Symfony Cache Adapter and specify if you want to use the production environment of the Payconiq API or the testing (Ext) environment.
```php
public function __construct(string $paymentProfileId, ClientInterface $httpClient = null, AdapterInterface $cache = null, bool $useProd = true)
```

The cache adapter is used to cache Payconiq's JWKS (JSON Web Key Set).
By default this library will use the `FilesystemAdapter` which will use the file system for caching.
If you'd like to use another caching system, like Redis for example, you can inject your own (e.g. `RedisAdapter`).

List of Symfony's Cache Adapters: https://symfony.com/doc/current/components/cache.html#available-cache-adapters

**Note**: when using the `PayconiqCallbackSignatureVerifier`, make sure your server time is correct because the verifier checks the issued-at header timestamp.

### PayconiqQrCodeGenerator
This class offers static functions to: 
- Customize (color, size, format) QR code links (Used for `Terminal & Display` & `Custom Online`)
- Generate static QR code stickers links (Used for `Static QR Sticker`)
- Generate QR code links with metadata, like: description, amount & reference (Used for `Receipt`, `Invoice` & `Top-up`)


## Some examples

### Request payment
```php
use Optios\Payconiq\PayconiqApiClient;
use Optios\Payconiq\Request\RequestPayment;

$apiKey = 'MY_PAYCONIQ_API_KEY';
$client = new PayconiqApiClient($apiKey, null, false);

$requestPayment = new RequestPayment(
    100 // = € 1
);
$requestPayment->setCallbackUrl('https://mywebsite.com/api/payconiq-webhook');
$requestPayment->setReference('ref123456');
$requestPayment->setPosId('POS00001');

$payment = $client->requestPayment($requestPayment);
var_dump($payment);
```

### Get payment
```php
use Optios\Payconiq\PayconiqApiClient;

$apiKey = 'MY_PAYCONIQ_API_KEY';
$client = new PayconiqApiClient($apiKey, null, false);

$payment = $client->getPayment('5bdb1685b93d1c000bde96f2');
var_dump($payment);
```

### Cancel payment
```php
use Optios\Payconiq\PayconiqApiClient;

$apiKey = 'MY_PAYCONIQ_API_KEY';
$client = new PayconiqApiClient($apiKey, null, false);

$client->cancelPayment('5bdb1685b93d1c000bde96f2');
```

### Search payments
```php
use Carbon\Carbon;
use Optios\Payconiq\PayconiqApiClient;
use Optios\Payconiq\Request\SearchPayments;

$apiKey = 'MY_PAYCONIQ_API_KEY';
$client = new PayconiqApiClient($apiKey, null, false);

$search = new SearchPayments(new Carbon('2020-12-01 00:00:00'));
$searchResult = $client->searchPayments($search);
var_dump($searchResult);
```

### Refund payment
```php
use Optios\Payconiq\PayconiqApiClient;

$apiKey = 'MY_PAYCONIQ_API_KEY';
$client = new PayconiqApiClient($apiKey, null, false);

$client->refundPayment('5bdb1685b93d1c000bde96f2');
```

### Verify callback (JWS)
```php
use Optios\Payconiq\PayconiqCallbackSignatureVerifier;

$paymentProfileId = '5fxxxxxxxxxxxf581'; //your payconiq payment profile id

// When Payconiq sends a POST to your webhook endpoint (callbackUrl), take the signature from the request header
// e.g. Symfony: Symfony\Component\HttpFoundation\Request $request->headers->get('signature');
$signature = 'eyJ0eXAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxbg8xg';

//POST body (payload)
$payload = '{"paymentId":"5bdb1685b93d1c000bde96f2","transferAmount":0,"tippingAmount":0,"amount":100,"totalAmount":0,"createdAt":"2020-12-01T10:22:40.487Z","expireAt":"2020-12-01T10:42:40.487Z","status":"EXPIRED","currency":"EUR"}';

$payconiqCallbackSignatureVerifier = new PayconiqCallbackSignatureVerifier($paymentProfileId, null, null, false);

echo $payconiqCallbackSignatureVerifier->isValid($signature, $payload) ? 'valid' : 'invalid';

var_dump($payconiqCallbackSignatureVerifier->loadAndVerifyJWS($signature, $payload));
```

### QR link generation
```php
use Optios\Payconiq\PayconiqQrCodeGenerator;

//Example 1: customized QR code (defaults are PNG, SMALL, MAGENTO)
//e.g. coming from Optios\Payconiq\Resource\Payment\Payment->getQrLink()
$qrLink = 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F73a222xxxxxxxxx00964';
$customizedQRLink  = PayconiqQrCodeGenerator::customizePaymentQrLink(
    $qrLink,
    PayconiqQrCodeGenerator::FORMAT_PNG,
    PayconiqQrCodeGenerator::SIZE_EXTRA_LARGE,
    PayconiqQrCodeGenerator::COLOR_BLACK
);
var_dump($customizedQRLink);

//Example 2: static QR code
$staticQRLink = PayconiqQrCodeGenerator::generateStaticQRCodeLink('abc123', 'POS00001');
var_dump($staticQRLink);
```

## Contributing
Feel free to submit pull requests for improvements & bug fixes.

please ensure your pull request adheres to the following guidelines:

* Enter a meaningful pull request description.
* Put a link to each library in your pull request ticket so it's easier to review.
* Use the following format for libraries: [LIBRARY](LINK) - DESCRIPTION.
* Make sure your text editor is set to remove trailing whitespace.

MIT License
