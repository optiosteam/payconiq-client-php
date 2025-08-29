<?php
declare(strict_types = 1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "../vendor/autoload.php";

use Optios\Payconiq\Enum\QrImageColor;
use Optios\Payconiq\Enum\QrImageFormat;
use Optios\Payconiq\Enum\QrImageSize;
use Optios\Payconiq\PayconiqQrCodeGenerator;

//Example 1: customized QR code (defaults are PNG, SMALL, MAGENTO)

//e.g. coming from Optios\Payconiq\Resource\Payment\Payment->getQrLink()
$qrLink = 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F73a222xxxxxxxxx00964';
$customizedQRLink  = PayconiqQrCodeGenerator::customizePaymentQrLink(
    $qrLink,
    QrImageFormat::PNG,
    QrImageSize::EXTRA_LARGE,
    QrImageColor::BLACK,
);
var_dump($customizedQRLink);

//Example 2: static QR code
$staticQRLink = PayconiqQrCodeGenerator::generateStaticQRCodeLink('abc123', 'POS00001');
var_dump($staticQRLink);
