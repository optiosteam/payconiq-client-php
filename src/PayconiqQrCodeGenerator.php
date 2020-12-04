<?php
declare(strict_types = 1);

namespace Optios\Payconiq;

use League\Url\Url;

/**
 * Class PayconiqQrCodeGenerator
 * @package Optios\Payconiq
 */
class PayconiqQrCodeGenerator
{
    public const PORTAL_URL                   = 'https://portal.payconiq.com/qrcode';
    public const LOCATION_URL_SCHEME_STATIC   = 'https://payconiq.com/l/1/';
    public const LOCATION_URL_SCHEME_METADATA = 'https://payconiq.com/t/1/';

    /*QR code image formats*/
    public const FORMAT_PNG = 'PNG';
    public const FORMAT_SVG = 'SVG';
    public const FORMATS    = [
        self::FORMAT_PNG,
        self::FORMAT_SVG,
    ];

    /*QR code image sizes*/
    public const SIZE_SMALL       = 'S';
    public const SIZE_MEDIUM      = 'M';
    public const SIZE_LARGE       = 'L';
    public const SIZE_EXTRA_LARGE = 'XL';
    public const SIZES            = [
        self::SIZE_SMALL,
        self::SIZE_MEDIUM,
        self::SIZE_LARGE,
        self::SIZE_EXTRA_LARGE,
    ];

    /*QR code color*/
    public const COLOR_BLACK   = 'black';
    public const COLOR_MAGENTA = 'magenta';
    public const COLORS        = [
        self::COLOR_BLACK,
        self::COLOR_MAGENTA,
    ];

    /**
     * Used for customizing QR links returned in the Payment.
     * Optios\Payconiq\Resource\Payment\Payment->getQrLink()
     * e.g. https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F38e150xxxxxxxxxxa0efe45
     *
     * Used for:
     * - Terminal & Display (https://developer.payconiq.com/online-payments-dock/#payconiq-instore-v3-terminal-display)
     * - Custom Online (https://developer.payconiq.com/online-payments-dock/#payconiq-online-v3-custom-online)
     *
     * @param string $qrLink
     * @param string $format
     * @param string $size
     * @param string $color
     *
     * @return string
     */
    public static function customizePaymentQrLink(
        string $qrLink,
        string $format = self::FORMAT_PNG,
        string $size = self::SIZE_SMALL,
        string $color = self::COLOR_MAGENTA
    ): string {
        $url = Url::createFromUrl($qrLink);

        if (in_array(strtoupper($format), self::FORMATS)) {
            $url->getQuery()->modify(['f' => $format]);
        }

        if (in_array(strtoupper($size), self::SIZES)) {
            $url->getQuery()->modify(['s' => $size]);
        }

        if (in_array(strtoupper($color), self::COLORS)) {
            $url->getQuery()->modify(['cl' => $color]);
        }

        return $url->__toString();
    }

    /**
     * Used for:
     * - Static QR Sticker (https://developer.payconiq.com/online-payments-dock/#payconiq-instore-v3-static-qr-sticker)
     *
     * @param string $paymentProfileId
     * @param string $posId
     * @param string $format
     * @param string $size
     * @param string $color
     *
     * @return string
     */
    public static function generateStaticQRCodeLink(
        string $paymentProfileId,
        string $posId,
        string $format = self::FORMAT_PNG,
        string $size = self::SIZE_SMALL,
        string $color = self::COLOR_MAGENTA
    ): string {
        $urlPayload = self::LOCATION_URL_SCHEME_STATIC . $paymentProfileId . '/' . $posId;

        $url = Url::createFromUrl(self::PORTAL_URL);
        $url->setQuery(['c' => $urlPayload]);

        return self::customizePaymentQrLink($url->__toString(), $format, $size, $color);
    }

    /**
     * Used for:
     * - Receipt (https://developer.payconiq.com/online-payments-dock/#payconiq-instore-v3-receipt)
     * - Invoice (https://developer.payconiq.com/online-payments-dock/#payconiq-invoice-v3-invoice)
     * - Top-up (https://developer.payconiq.com/online-payments-dock/#payconiq-online-v3-top-up)
     *
     * @param string      $paymentProfileId
     * @param string|null $description
     * @param int|null    $amount
     * @param string|null $reference
     * @param string      $format
     * @param string      $size
     * @param string      $color
     *
     * @return string
     */
    public static function generateQRCodeWithMetadata(
        string $paymentProfileId,
        ?string $description,
        ?int $amount,
        ?string $reference,
        string $format = self::FORMAT_PNG,
        string $size = self::SIZE_SMALL,
        string $color = self::COLOR_MAGENTA
    ): string {
        $urlPayload = Url::createFromUrl(self::LOCATION_URL_SCHEME_METADATA . $paymentProfileId);

        if (! empty($description)) {
            $urlPayload->setQuery(['D' => $description]);
        }

        if (null !== $amount) {
            $urlPayload->setQuery(['A' => $amount]);
        }

        if (! empty($reference)) {
            $urlPayload->setQuery(['R' => $reference]);
        }

        $url = Url::createFromUrl(self::PORTAL_URL);
        $url->setQuery(['c' => $urlPayload]);

        return self::customizePaymentQrLink($url->__toString(), $format, $size, $color);
    }
}
