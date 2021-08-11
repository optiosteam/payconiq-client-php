<?php

namespace Tests\Optios\Payconiq;

use Optios\Payconiq\PayconiqQrCodeGenerator;
use PHPUnit\Framework\TestCase;

class PayconiqQrCodeGeneratorTest extends TestCase
{
    /**
     * @param array  $data
     * @param string $expected
     *
     * @dataProvider getCustomizePaymentQrLinkData
     */
    public function testCustomizePaymentQrLink(array $data, string $expected)
    {
        $this->assertEquals($expected,
            PayconiqQrCodeGenerator::customizePaymentQrLink(
                $data[ 'link' ],
                $data[ 'format' ],
                $data[ 'size' ],
                $data[ 'color' ]
            )
        );
    }

    /**
     * @return array[]
     */
    public function getCustomizePaymentQrLinkData(): array
    {
        //phpcs:disable
        $qrLink = 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F73a222957726d63d26400964';

        return [
            'Default' => [
                'data' => [
                    'link' => $qrLink,
                    'format' => PayconiqQrCodeGenerator::FORMAT_PNG,
                    'size' => PayconiqQrCodeGenerator::SIZE_SMALL,
                    'color' => PayconiqQrCodeGenerator::COLOR_MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F73a222957726d63d26400964&f=PNG&s=S&cl=magenta',
            ],
            'SVG Medium Black' => [
                'data' => [
                    'link' => $qrLink,
                    'format' => PayconiqQrCodeGenerator::FORMAT_SVG,
                    'size' => PayconiqQrCodeGenerator::SIZE_MEDIUM,
                    'color' => PayconiqQrCodeGenerator::COLOR_BLACK,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F73a222957726d63d26400964&f=SVG&s=M&cl=black',
            ],
            'PNG Large Magenta' => [
                'data' => [
                    'link' => $qrLink,
                    'format' => PayconiqQrCodeGenerator::FORMAT_PNG,
                    'size' => PayconiqQrCodeGenerator::SIZE_LARGE,
                    'color' => PayconiqQrCodeGenerator::COLOR_MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F73a222957726d63d26400964&f=PNG&s=L&cl=magenta',
            ],
        ];
        //phpcs:enable
    }

    /**
     * @param array  $data
     * @param string $expected
     *
     * @dataProvider getGenerateStaticQRCodeLinkData
     */
    public function testGenerateStaticQRCodeLink(array $data, string $expected)
    {
        $this->assertEquals($expected,
            PayconiqQrCodeGenerator::generateStaticQRCodeLink(
                $data[ 'payment_profile_id' ],
                $data[ 'pos_id' ],
                $data[ 'format' ],
                $data[ 'size' ],
                $data[ 'color' ]
            )
        );
    }

    /**
     * @return array[]
     */
    public function getGenerateStaticQRCodeLinkData(): array
    {
        //phpcs:disable
        $paymentProfileId = 'abc123';
        $posId            = 'POS0001';

        return [
            'Default' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'pos_id' => $posId,
                    'format' => PayconiqQrCodeGenerator::FORMAT_PNG,
                    'size' => PayconiqQrCodeGenerator::SIZE_SMALL,
                    'color' => PayconiqQrCodeGenerator::COLOR_MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fl%2F1%2Fabc123%2FPOS0001&f=PNG&s=S&cl=magenta',
            ],
            'SVG Medium Black' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'pos_id' => $posId,
                    'format' => PayconiqQrCodeGenerator::FORMAT_SVG,
                    'size' => PayconiqQrCodeGenerator::SIZE_MEDIUM,
                    'color' => PayconiqQrCodeGenerator::COLOR_BLACK,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fl%2F1%2Fabc123%2FPOS0001&f=SVG&s=M&cl=black',
            ],
            'PNG Large Magenta' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'pos_id' => $posId,
                    'format' => PayconiqQrCodeGenerator::FORMAT_PNG,
                    'size' => PayconiqQrCodeGenerator::SIZE_LARGE,
                    'color' => PayconiqQrCodeGenerator::COLOR_MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fl%2F1%2Fabc123%2FPOS0001&f=PNG&s=L&cl=magenta',
            ],
        ];
        //phpcs:enable
    }

    /**
     * @param array $data
     * @param mixed $expected
     *
     * @dataProvider getGenerateQRCodeWithMetadataData
     */
    public function testGenerateQRCodeWithMetadata(array $data, $expected)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage($expected->getMessage());
        }

        $result = PayconiqQrCodeGenerator::generateQRCodeWithMetadata(
            $data[ 'payment_profile_id' ],
            $data[ 'description' ],
            $data[ 'amount' ],
            $data[ 'reference' ],
            $data[ 'format' ],
            $data[ 'size' ],
            $data[ 'color' ]
        );

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array[]
     */
    public function getGenerateQRCodeWithMetadataData(): array
    {
        //phpcs:disable
        $paymentProfileId = 'abc123';

        return [
            'Default with Amount and Reference' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => null,
                    'amount' => 1000,
                    'reference' => '#123.abc!@',
                    'format' => PayconiqQrCodeGenerator::FORMAT_PNG,
                    'size' => PayconiqQrCodeGenerator::SIZE_SMALL,
                    'color' => PayconiqQrCodeGenerator::COLOR_MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Ft%2F1%2Fabc123%3FA%3D1000%26R%3D%2523123.abc%2521%2540&f=PNG&s=S&cl=magenta',
            ],
            'SVG Medium Black with Description, Amount and Reference' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => 'please pay me',
                    'amount' => 1000,
                    'reference' => '#123.abc!@--%123--',
                    'format' => PayconiqQrCodeGenerator::FORMAT_SVG,
                    'size' => PayconiqQrCodeGenerator::SIZE_MEDIUM,
                    'color' => PayconiqQrCodeGenerator::COLOR_BLACK,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Ft%2F1%2Fabc123%3FD%3Dplease%2520pay%2520me%26A%3D1000%26R%3D%2523123.abc%2521%2540--%2525123--&f=SVG&s=M&cl=black',
            ],
            'PNG Large Magenta with Amount' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => null,
                    'amount' => 9900,
                    'reference' => null,
                    'format' => PayconiqQrCodeGenerator::FORMAT_PNG,
                    'size' => PayconiqQrCodeGenerator::SIZE_LARGE,
                    'color' => PayconiqQrCodeGenerator::COLOR_MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Ft%2F1%2Fabc123%3FA%3D9900&f=PNG&s=L&cl=magenta',
            ],
            'Expect Exception for too long description' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
                    'amount' => 9900,
                    'reference' => null,
                    'format' => PayconiqQrCodeGenerator::FORMAT_PNG,
                    'size' => PayconiqQrCodeGenerator::SIZE_LARGE,
                    'color' => PayconiqQrCodeGenerator::COLOR_MAGENTA,
                ],
                'expected' => new \InvalidArgumentException('Description max length is 35 characters'),
            ],
            'Expect Exception for too small amount' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => 'xxx',
                    'amount' => 0,
                    'reference' => null,
                    'format' => PayconiqQrCodeGenerator::FORMAT_PNG,
                    'size' => PayconiqQrCodeGenerator::SIZE_LARGE,
                    'color' => PayconiqQrCodeGenerator::COLOR_MAGENTA,
                ],
                'expected' => new \InvalidArgumentException('Amount must be between 1 - 999999 Euro cents'),
            ],
            'Expect Exception for too big amount' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => 'xxx',
                    'amount' => 10000000000,
                    'reference' => null,
                    'format' => PayconiqQrCodeGenerator::FORMAT_PNG,
                    'size' => PayconiqQrCodeGenerator::SIZE_LARGE,
                    'color' => PayconiqQrCodeGenerator::COLOR_MAGENTA,
                ],
                'expected' => new \InvalidArgumentException('Amount must be between 1 - 999999 Euro cents'),
            ],
            'Expect Exception for too long reference' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => 'xxx',
                    'amount' => 9900,
                    'reference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
                    'format' => PayconiqQrCodeGenerator::FORMAT_PNG,
                    'size' => PayconiqQrCodeGenerator::SIZE_LARGE,
                    'color' => PayconiqQrCodeGenerator::COLOR_MAGENTA,
                ],
                'expected' => new \InvalidArgumentException('Reference max length is 35 characters'),
            ],
        ];
        //phpcs:enable
    }
}
