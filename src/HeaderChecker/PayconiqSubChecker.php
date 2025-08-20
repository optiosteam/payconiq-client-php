<?php
declare(strict_types=1);

namespace Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidHeaderException;

/**
 * Class PayconiqSubChecker
 * @package Optios\Payconiq\HeaderChecker
 */
final readonly class PayconiqSubChecker implements HeaderChecker
{
    private const HEADER_NAME = 'https://payconiq.com/sub';

    public function __construct(
        private string $paymentProfileId,
    ) {
    }

    /**
     * {@inheritdoc}
     * @throws InvalidHeaderException
     */
    public function checkHeader($value): void {
        if (false === is_string($value)) {
            throw new InvalidHeaderException(
                message: sprintf('"%s" must be a string.', self::HEADER_NAME),
                header: self::HEADER_NAME,
                value: $value,
            );
        }

        if ($value !== $this->paymentProfileId) {
            throw new InvalidHeaderException(
                message: sprintf('"%s" should match the Payment profile ID', self::HEADER_NAME),
                header: self::HEADER_NAME,
                value: $value,
            );
        }
    }

    public function supportedHeader(): string {
        return self::HEADER_NAME;
    }

    public function protectedHeaderOnly(): bool {
        return false;
    }
}
