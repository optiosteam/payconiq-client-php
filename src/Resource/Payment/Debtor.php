<?php
declare(strict_types=1);

namespace Optios\Payconiq\Resource\Payment;

final readonly class Debtor
{
    private function __construct(
        private ?string $name,
        private ?string $iban,
    )
    {
    }

    /**
     * @deprecated Use createFromObject() instead.
     */
    public static function createFromStdClass(\stdClass $class): self
    {
        return self::createFromObject($class);
    }

    public static function createFromObject(object $obj): self
    {
        return new self(
            name: $obj->name ?? null,
            iban: $obj->iban ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'iban' => $this->iban,
        ];
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }
}
