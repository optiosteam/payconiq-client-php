<?php
declare(strict_types = 1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "../vendor/autoload.php";

use Optios\Payconiq\PayconiqApiClient;
use Optios\Payconiq\Request\RequestPayment;

$apiKey = 'MY_PAYCONIQ_API_KEY';
$client = new PayconiqApiClient($apiKey, null, false);

$requestPayment1 = new RequestPayment(
    100 // = € 1
);
$requestPayment1->setCallbackUrl('https://mywebsite.com/api/payconiq-webhook');
$requestPayment1->setReference('ref123456');
$requestPayment1->setPosId('POS00001');

$payment1 = $client->requestPayment($requestPayment1);
var_dump($payment1);

$requestPayment2 = new RequestPayment(
    2500 // = € 25
);
$payment2 = $client->requestPayment($requestPayment2);
var_dump($payment2);
