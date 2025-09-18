<?php
declare(strict_types = 1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "../vendor/autoload.php";

use Optios\Payconiq\PayconiqCallbackSignatureVerifier;

$paymentProfileId = '5fxxxxxxxxxxxf581'; //your payconiq payment profile id

// When Payconiq sends a POST to your webhook endpoint (callbackUrl), take the signature from the request header
// e.g. Symfony: Symfony\Component\HttpFoundation\Request $request->headers->get('signature');
$signature = 'eyJ0eXAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxbg8xg';

//POST body (payload)
$payload = '{"paymentId":"5bdb1685b93d1c000bde96f2","amount":100,"createdAt":"2020-12-01T10:22:40.487Z","expireAt":"2020-12-01T10:42:40.487Z","status":"EXPIRED","currency":"EUR"}';

$payconiqCallbackSignatureVerifier = new PayconiqCallbackSignatureVerifier($paymentProfileId, null, null, false);

echo $payconiqCallbackSignatureVerifier->isValid($signature, $payload) ? 'valid' : 'invalid';

var_dump($payconiqCallbackSignatureVerifier->loadAndVerifyJWS($signature, $payload));
