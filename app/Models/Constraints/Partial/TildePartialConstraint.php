<?php

namespace App\Models\Constraints\Partial;

use App\Enums\SingleConstraintType;
use App\Models\Constraints\SingleConstraint;

readonly class TildePartialConstraint extends PartialConstraint
{
    // ~1.2
    // --- to ---
    // >=1.2.0 , <2.0.0

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
        $major = $this->major;
        $minor = $this->minor;
        $patch = $this->patch;

        // ~1 => ~1.0
        if($minor === null) {
            $minor = 0;
        }

        // ~1.2 => <2.0.0
        if($patch === null) {
            $major += 1;
            $minor = 0;
        }
        // ~1.2.3 => <1.3
        else {
            $minor += 1;
        }

        return new SingleConstraint(
            type: SingleConstraintType::LessThan,
            major: $major,
            minor: $minor,
            patch: 0,
        );
    }
}
