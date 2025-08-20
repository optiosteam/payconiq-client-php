<?php

namespace Tests\Optios\Payconiq;

use Carbon\CarbonImmutable;
use Optios\Payconiq\Enum\QrImageColor;
use Optios\Payconiq\Enum\QrImageFormat;
use Optios\Payconiq\Enum\QrImageSize;
use Optios\Payconiq\MigrationHelper;
use Optios\Payconiq\PayconiqQrCodeGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LegacyEndpointPayconiqQrCodeGeneratorTest extends TestCase
{
    protected function setUp(): void {
        parent::setUp();

        CarbonImmutable::setTestNow(CarbonImmutable::parse('2025-09-01 12:00:00', MigrationHelper::TIMEZONE));
    }

    protected function tearDown(): void {
        CarbonImmutable::setTestNow();
    }

    #[DataProvider('getCustomizePaymentQrLinkData')]
    public function testCustomizePaymentQrLink(array $data, string $expected) {
        $this->assertEquals($expected,
            PayconiqQrCodeGenerator::customizePaymentQrLink(
                $data['link'],
                $data['format'],
                $data['size'],
                $data['color'],
            ),
        );
    }

    public static function getCustomizePaymentQrLinkData(): array {
        //phpcs:disable
        $qrLink = 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F73a222957726d63d26400964';

        return [
            'Customize - Default' => [
                'data' => [
                    'link' => $qrLink,
                    'format' => QrImageFormat::PNG,
                    'size' => QrImageSize::SMALL,
                    'color' => QrImageColor::MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F73a222957726d63d26400964&f=PNG&s=S&cl=magenta',
            ],
            'Customize - SVG Medium Black' => [
                'data' => [
                    'link' => $qrLink,
                    'format' => QrImageFormat::SVG,
                    'size' => QrImageSize::MEDIUM,
                    'color' => QrImageColor::BLACK,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F73a222957726d63d26400964&f=SVG&s=M&cl=black',
            ],
            'Customize - PNG Large Magenta' => [
                'data' => [
                    'link' => $qrLink,
                    'format' => QrImageFormat::PNG,
                    'size' => QrImageSize::LARGE,
                    'color' => QrImageColor::MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fpay%2F2%2F73a222957726d63d26400964&f=PNG&s=L&cl=magenta',
            ],
        ];
        //phpcs:enable
    }

    #[DataProvider('getGenerateStaticQRCodeLinkData')]
    public function testGenerateStaticQRCodeLink(array $data, string $expected) {
        $this->assertEquals($expected,
            PayconiqQrCodeGenerator::generateStaticQRCodeLink(
                $data['payment_profile_id'],
                $data['pos_id'],
                $data['format'],
                $data['size'],
                $data['color'],
            ),
        );
    }

    public static function getGenerateStaticQRCodeLinkData(): array {
        //phpcs:disable
        $paymentProfileId = 'abc123';
        $posId = 'POS0001';

        return [
            'Static - Default' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'pos_id' => $posId,
                    'format' => QrImageFormat::PNG,
                    'size' => QrImageSize::SMALL,
                    'color' => QrImageColor::MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fl%2F1%2Fabc123%2FPOS0001&f=PNG&s=S&cl=magenta',
            ],
            'Static - SVG Medium Black' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'pos_id' => $posId,
                    'format' => QrImageFormat::SVG,
                    'size' => QrImageSize::MEDIUM,
                    'color' => QrImageColor::BLACK,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fl%2F1%2Fabc123%2FPOS0001&f=SVG&s=M&cl=black',
            ],
            'Static - PNG Large Magenta' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'pos_id' => $posId,
                    'format' => QrImageFormat::PNG,
                    'size' => QrImageSize::LARGE,
                    'color' => QrImageColor::MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Fl%2F1%2Fabc123%2FPOS0001&f=PNG&s=L&cl=magenta',
            ],
        ];
        //phpcs:enable
    }

    #[DataProvider('getGenerateQRCodeWithMetadataData')]
    public function testGenerateQRCodeWithMetadata(array $data, $expected) {
        if ($expected instanceof \Exception) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage($expected->getMessage());
        }

        $result = PayconiqQrCodeGenerator::generateQRCodeWithMetadata(
            $data['payment_profile_id'],
            $data['description'],
            $data['amount'],
            $data['reference'],
            $data['format'],
            $data['size'],
            $data['color'],
        );

        $this->assertEquals($expected, $result);
    }

    public static function getGenerateQRCodeWithMetadataData(): array {
        //phpcs:disable
        $paymentProfileId = 'abc123';

        return [
            'Metadata - Default with Amount and Reference' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => null,
                    'amount' => 1000,
                    'reference' => '#123.abc!@',
                    'format' => QrImageFormat::PNG,
                    'size' => QrImageSize::SMALL,
                    'color' => QrImageColor::MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Ft%2F1%2Fabc123%3FA%3D1000%26R%3D%2523123.abc%2521%2540&f=PNG&s=S&cl=magenta',
            ],
            'Metadata - SVG Medium Black with Description, Amount and Reference' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => 'please pay me',
                    'amount' => 1000,
                    'reference' => '#123.abc!@--%123--',
                    'format' => QrImageFormat::SVG,
                    'size' => QrImageSize::MEDIUM,
                    'color' => QrImageColor::BLACK,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Ft%2F1%2Fabc123%3FD%3Dplease%2520pay%2520me%26A%3D1000%26R%3D%2523123.abc%2521%2540--%2525123--&f=SVG&s=M&cl=black',
            ],
            'Metadata - PNG Large Magenta with Amount' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => null,
                    'amount' => 9900,
                    'reference' => null,
                    'format' => QrImageFormat::PNG,
                    'size' => QrImageSize::LARGE,
                    'color' => QrImageColor::MAGENTA,
                ],
                'expected' => 'https://portal.payconiq.com/qrcode?c=https%3A%2F%2Fpayconiq.com%2Ft%2F1%2Fabc123%3FA%3D9900&f=PNG&s=L&cl=magenta',
            ],
            'Metadata - Expect Exception for too long description' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
                    'amount' => 9900,
                    'reference' => null,
                    'format' => QrImageFormat::PNG,
                    'size' => QrImageSize::LARGE,
                    'color' => QrImageColor::MAGENTA,
                ],
                'expected' => new \InvalidArgumentException('Description max length is 35 characters'),
            ],
            'Metadata - Expect Exception for too small amount' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => 'xxx',
                    'amount' => 0,
                    'reference' => null,
                    'format' => QrImageFormat::PNG,
                    'size' => QrImageSize::LARGE,
                    'color' => QrImageColor::MAGENTA,
                ],
                'expected' => new \InvalidArgumentException('Amount must be between 1 - 999999 Euro cents'),
            ],
            'Metadata - Expect Exception for too big amount' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => 'xxx',
                    'amount' => 10000000000,
                    'reference' => null,
                    'format' => QrImageFormat::PNG,
                    'size' => QrImageSize::LARGE,
                    'color' => QrImageColor::MAGENTA,
                ],
                'expected' => new \InvalidArgumentException('Amount must be between 1 - 999999 Euro cents'),
            ],
            'Metadata - Expect Exception for too long reference' => [
                'data' => [
                    'payment_profile_id' => $paymentProfileId,
                    'description' => 'xxx',
                    'amount' => 9900,
                    'reference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
                    'format' => QrImageFormat::PNG,
                    'size' => QrImageSize::LARGE,
                    'color' => QrImageColor::MAGENTA,
                ],
                'expected' => new \InvalidArgumentException('Reference max length is 35 characters'),
            ],
        ];
        //phpcs:enable
    }
}
