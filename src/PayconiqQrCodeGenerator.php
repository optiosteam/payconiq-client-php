<?php
declare(strict_types = 1);

namespace Optios\Payconiq;

use League\Uri\Components\Query;

//use League\Uri\Parser\QueryString;
use League\Uri\Uri;
use League\Uri\QueryString;
use League\Uri\UriModifier;

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
        $uri   = Uri::createFromString($qrLink);
        $query = Query::createFromUri($uri);

        if (in_array(strtoupper($format), self::FORMATS)) {
            $query = $query->merge('f=' . $format);
        }

        if (in_array(strtoupper($size), self::SIZES)) {
            $query = $query->merge('s=' . $size);
        }

        if (in_array(strtolower($color), self::COLORS)) {
            $query = $query->merge('cl=' . $color);
        }

        return (string) $uri->withQuery($query->toRFC1738());
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

        $uri = Uri::createFromString(self::PORTAL_URL);
        $uri = UriModifier::appendQuery($uri, 'c=' . $urlPayload);

        return self::customizePaymentQrLink((string) $uri, $format, $size, $color);
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
        string  $paymentProfileId,
        ?string $description,
        ?int    $amount,
        ?string $reference,
        string  $format = self::FORMAT_PNG,
        string  $size = self::SIZE_SMALL,
        string  $color = self::COLOR_MAGENTA
    ): string {
        $uriPayload   = Uri::createFromString(self::LOCATION_URL_SCHEME_METADATA . $paymentProfileId);
        $queryPayload = Query::createFromUri($uriPayload);

        $test  = [];
        $test2 = [];

        if (! empty($description)) {
            if (strlen($description) > 35) {
                throw new \InvalidArgumentException('Description max length is 35 characters');
            }

            $queryPayload = $queryPayload->merge('D=' . $description);
            $test[]       = ['D', $description];
            $test2[ 'D' ] = $description;
        }

        if (null !== $amount) {
            if ($amount < 1 || $amount > 999999) {
                throw new \InvalidArgumentException('Amount must be between 1 - 999999 Euro cents');
            }

            $queryPayload = $queryPayload->merge('A=' . $amount);
            $test[]       = ['A', $amount];
            $test2[ 'A' ] = $amount;
        }

        if (! empty($reference)) {
            if (strlen($reference) > 35) {
                throw new \InvalidArgumentException('Reference max length is 35 characters');
            }

            $queryPayload = $queryPayload->merge('R=' . $reference);
            $test[]       = ['R', $reference];
            $test2[ 'R' ] = $reference;
        }

//        $url = Url::createFromUrl(self::PORTAL_URL);
//        $url->setQuery(['c' => $urlPayload->__toString()]);


        $queryString = $queryPayload->toRFC1738(); //todo:remove
        echo '---  queryString ----';
        var_dump($queryString);

        $queryString2 = $queryPayload->getContent(); //todo:remove
        echo '---  queryString2 ----';
        var_dump($queryString2);


        $fullPayloadUrl = (string) $uriPayload->withQuery($queryPayload->toRFC1738());

        $fullPayloadUrl2 = (string) $uriPayload->withQuery(QueryString::build($test));
        $fullPayloadUrl3 = (string) $uriPayload->withQuery(QueryString::build($test, '&', PHP_QUERY_RFC1738));

        $fullPayloadUrl4 = (string) $uriPayload->withQuery(\League\Uri\Parser\QueryString::build($test));
        $fullPayloadUrl5 = (string) $uriPayload->withQuery(\League\Uri\Parser\QueryString::build($test,
            '&',
            PHP_QUERY_RFC1738));


        echo '--- fullPayloadUrl 1 ----';
        var_dump($fullPayloadUrl);
        echo '--- fullPayloadUrl 2 ----';
        var_dump($fullPayloadUrl2);
        echo '--- fullPayloadUrl 3 ----';
        var_dump($fullPayloadUrl3);
        echo '--- fullPayloadUrl 4 ----';
        var_dump($fullPayloadUrl4);
        echo '--- fullPayloadUrl 5 ----';
        var_dump($fullPayloadUrl5);

        $uri = Uri::createFromString(self::PORTAL_URL);
//        (string) $uri->withQuery('c=' . $fullPayloadUrl);
        $query = Query::createFromUri($uri);

        echo '---  queryString pre-append ----';
        var_dump($query->getContent());

        $query = $query->append('c=' . $fullPayloadUrl);

        echo '---  queryString post-append : getContent() ----';
        var_dump($query->getContent());

        echo '---  queryString post-append : toRFC1738() ----';
        var_dump($query->toRFC1738());

        echo '---  queryString post-append : toRFC3986() ----';
        var_dump($query->toRFC3986());

//        $link = (string) $uri->withQuery($query->toRFC1738());
        $link = self::PORTAL_URL . '?c=' . $fullPayloadUrl;
        echo '---  2 ----';
        var_dump($link);


        //----------------


//        QueryString::build()


        return self::customizePaymentQrLink($link, $format, $size, $color);
    }
}
