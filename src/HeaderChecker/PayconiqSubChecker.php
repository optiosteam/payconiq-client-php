<?php
declare(strict_types = 1);

namespace Optios\Payconiq\HeaderChecker;

use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidHeaderException;

/**
 * Class PayconiqSubChecker
 * @package Optios\Payconiq\HeaderChecker
 */
final class PayconiqSubChecker implements HeaderChecker
{
    private const HEADER_NAME = 'https://payconiq.com/sub';

    /**
     * @var string
     */
    private $merchantProfileId;

    /**
     * PayconiqSubChecker constructor.
     *
     * @param string $merchantProfileId
     */
    public function __construct(string $merchantProfileId)
    {
        $this->merchantProfileId = $merchantProfileId;
    }

    /**
     * {@inheritdoc}
     */
    public function checkHeader($value): void
    {
        if (! is_string($value)) {
            throw new InvalidHeaderException(
                sprintf('"%s" must be a string.', self::HEADER_NAME),
                self::HEADER_NAME,
                $value
            );
        }

        //todo: uncomment when we get the correct merchantProfileId
//        if ($value !== $this->merchantProfileId) {
//            throw new InvalidHeaderException(
//                sprintf('"%s" should match the merchant profile ID', self::HEADER_NAME),
//                self::HEADER_NAME,
//                $value
//            );
//        }
    }

    public function supportedHeader(): string
    {
        return self::HEADER_NAME;
    }

    public function protectedHeaderOnly(): bool
    {
        return false;
    }
}
