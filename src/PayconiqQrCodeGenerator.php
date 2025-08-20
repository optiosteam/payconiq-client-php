<?php

declare(strict_types=1);

namespace Optios\Payconiq;

use League\Uri\Http;
use League\Uri\Modifier;
use Optios\Payconiq\Enum\QrImageColor;
use Optios\Payconiq\Enum\QrImageFormat;
use Optios\Payconiq\Enum\QrImageSize;

class PayconiqQrCodeGenerator
{
    public const PORTAL_URL_LEGACY = 'https://portal.payconiq.com/qrcode';
    public const PORTAL_URL_NEW = 'https://qrcodegenerator.api.bancontact.net/qrcode';

    public const LOCATION_URL_SCHEME_STATIC = 'https://payconiq.com/l/1/';
    public const LOCATION_URL_SCHEME_METADATA = 'https://payconiq.com/t/1/';

    private static function getEndpoint(): string
    {
        if (true === MigrationHelper::switchToNewEndpoints()) {
            // new endpoints
            return self::PORTAL_URL_NEW;
        }

        // legacy endpoints
        return self::PORTAL_URL_LEGACY;
    }

    /**
     * Used for customizing QR links returned in the Payment.
     * Optios\Payconiq\Resource\Payment\Payment->getQrLink()
     * e.g. https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F38e150xxxxxxxxxxa0efe45
     *
     * Used for:
     * - Terminal & Display (https://developer.payconiq.com/online-payments-dock/#payconiq-instore-v3-terminal-display)
     * - Custom Online (https://developer.payconiq.com/online-payments-dock/#payconiq-online-v3-custom-online)
     */
    public static function customizePaymentQrLink(
        string $qrLink,
        QrImageFormat $format = QrImageFormat::PNG,
        QrImageSize $size = QrImageSize::SMALL,
        QrImageColor $color = QrImageColor::MAGENTA,
    ): string {
        return (string) Modifier::from(Http::new($qrLink))
            ->mergeQueryParameters([
                'f' => $format->value,
                's' => $size->value,
                'cl' => $color->value,
            ])
            ->getUri();
    }

    /**
     * Used for:
     * - Static QR Sticker (https://developer.payconiq.com/online-payments-dock/#payconiq-instore-v3-static-qr-sticker)
     */
    public static function generateStaticQRCodeLink(
        string $paymentProfileId,
        string $posId,
        QrImageFormat $format = QrImageFormat::PNG,
        QrImageSize $size = QrImageSize::SMALL,
        QrImageColor $color = QrImageColor::MAGENTA,
    ): string {
        $urlPayload = self::LOCATION_URL_SCHEME_STATIC . $paymentProfileId . '/' . $posId;

        $uri = Modifier::from(Http::new(self::getEndpoint()))
            ->mergeQueryParameters(['c' => $urlPayload])
            ->getUri();

        return self::customizePaymentQrLink((string) $uri, $format, $size, $color);
    }

    /**
     * Used for:
     * - Receipt (https://developer.payconiq.com/online-payments-dock/#payconiq-instore-v3-receipt)
     * - Invoice (https://developer.payconiq.com/online-payments-dock/#payconiq-invoice-v3-invoice)
     * - Top-up (https://developer.payconiq.com/online-payments-dock/#payconiq-online-v3-top-up)
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function generateQRCodeWithMetadata(
        string $paymentProfileId,
        ?string $description,
        ?int $amount,
        ?string $reference,
        QrImageFormat $format = QrImageFormat::PNG,
        QrImageSize $size = QrImageSize::SMALL,
        QrImageColor $color = QrImageColor::MAGENTA,
    ): string {
        $payloadUri = Http::new(self::LOCATION_URL_SCHEME_METADATA . $paymentProfileId);

        $query = [];
        if (null !== $description && $description !== '') {
            if (strlen($description) > 35) {
                throw new \InvalidArgumentException('Description max length is 35 characters');
            }

            $query['D'] = $description;
        }

        if (null !== $amount) {
            if ($amount < 1 || $amount > 999999) {
                throw new \InvalidArgumentException('Amount must be between 1 - 999999 Euro cents');
            }

            $query['A'] = $amount;
        }

        if (null !== $reference && $reference !== '') {
            if (strlen($reference) > 35) {
                throw new \InvalidArgumentException('Reference max length is 35 characters');
            }

            $query['R'] = $reference;
        }

        if (false === empty($query)) {
            $payloadUri = Modifier::from($payloadUri)->mergeQueryParameters($query)->getUri();
        }

        $uri = Modifier::from(Http::new(self::getEndpoint()))
            ->mergeQueryParameters(['c' => (string) $payloadUri])
            ->getUri();

        return self::customizePaymentQrLink((string) $uri, $format, $size, $color);
    }
}
