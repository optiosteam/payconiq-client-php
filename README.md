# PHP Payconiq API Client (unofficial)

**THIS REPOSITORY IS STILL A WORK IN PROGRESS. NOT ALL API FUNCTIONS ARE SUPPORTED YET.**

Supported API version: v3

Development sponsored by [Optios](https://www.optios.net)

API Documentation: https://developer.payconiq.com/online-payments-dock/#payment-api-version-3-v3-

## Installation

**Requirement**: PHP version >=7.2

```
composer require optiosteam/payconiq-client-php
```

## Some examples

### Create payment
```php
use Optios\Payconiq\PayconiqApiClient;
use Optios\Payconiq\Request\CreatePayment;

$apiKey = 'MY_PAYCONIQ_API_KEY';
$client = new PayconiqApiClient($apiKey, null, false);

$createPayment = new CreatePayment(
    100 // = € 1
);
$createPayment->setCallbackUrl('https://mywebsite.com/api/payconiq-webhook');
$createPayment->setReference('ref123456');
$createPayment->setPosId('POS00001');

$payment = $client->createPayment($createPayment);
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

$merchantProfileId = '5fxxxxxxxxxxxf581'; //your payconiq merchant id

// When Payconiq sends a POST to your webhook endpoint (callbackUrl), take the signature from the request header
// e.g. Symfony: Symfony\Component\HttpFoundation\Request $request->headers->get('signature');
$signature = 'eyJ0eXAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxbg8xg';

//POST body (payload)
$payload = '{"paymentId":"5bdb1685b93d1c000bde96f2","transferAmount":0,"tippingAmount":0,"amount":100,"totalAmount":0,"createdAt":"2020-12-01T10:22:40.487Z","expireAt":"2020-12-01T10:42:40.487Z","status":"EXPIRED","currency":"EUR"}';

$payconiqCallbackSignatureVerifier = new PayconiqCallbackSignatureVerifier($merchantProfileId, null, null, false);

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


Feel free to submit pull requests for improvements & bugfixes.

MIT License
