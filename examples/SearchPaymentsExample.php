<?php
declare(strict_types = 1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "../vendor/autoload.php";

use Carbon\Carbon;
use Optios\Payconiq\PayconiqApiClient;
use Optios\Payconiq\Request\SearchPayments;

$apiKey = 'MY_PAYCONIQ_API_KEY';

$client = new PayconiqApiClient($apiKey, null, false);
$search = new SearchPayments(new Carbon('2020-12-01 00:00:00'));
$searchResult = $client->searchPayments($search);
var_dump($searchResult);
