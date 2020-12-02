<?php
declare(strict_types = 1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "../vendor/autoload.php";

use Optios\Payconiq\PayconiqApiClient;
use Optios\Payconiq\Request\CreatePayment;

$apiKey = 'MY_PAYCONIQ_API_KEY';

$client = new PayconiqApiClient($apiKey, null, false);

$createPayment1 = new CreatePayment(
    100, // = € 1
    'EUR',
    'https://mywebsite.com/api/payconiq-webhook',
    'ref123456',
    null,
    null,
    'POS00001'
);
$payment1 = $client->createPayment($createPayment1);
var_dump($payment1);

$createPayment2 = new CreatePayment(
    2500, // = € 25
    'EUR',
    null,
    null,
    null,
    null,
    'POS00002'
);
$payment2 = $client->createPayment($createPayment2);
var_dump($payment2);
