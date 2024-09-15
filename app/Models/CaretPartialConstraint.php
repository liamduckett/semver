<?php

namespace App\Models;

use App\Enums\SingleConstraintType;

readonly class CaretPartialConstraint extends PartialConstraint
{
    // ^1.2.3
    // --- to ---
    // >=1.2.3 , <2.0.0

    public function __construct(
        public int $major,
        public int|null $minor,
        public int|null $patch,
    ) {}

    public static function fromString(string $input): self
    {
        $input = substr($input, 1);
        $versionParts = explode('.', $input);
        $versionParts = array_pad($versionParts, 3, null);
        [$major, $minor, $patch] = $versionParts;

        return new self(
            major: $major,
            minor: $minor,
            patch: $patch,
        );
    }

    public function minimum(): SingleConstraint
    {
        return new SingleConstraint(
            type: SingleConstraintType::GreaterThanOrEqualTo,
            major: $this->major,
            minor: $this->minor ?? 0,
            patch: $this->patch ?? 0,
        );
    }

    public function maximum(): SingleConstraint
    {
        return new SingleConstraint(
            type: SingleConstraintType::LessThan,
            major: $this->major + 1,
            minor: 0,
            patch: 0,
        );
    }
}
