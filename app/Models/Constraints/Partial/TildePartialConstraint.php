<?php

namespace App\Models\Constraints\Partial;

use App\Enums\Operator;
use App\Enums\SingleConstraintType;
use App\Models\Constraints\GroupConstraint;
use App\Models\Constraints\PartialConstraint;
use App\Models\Constraints\SingleConstraint;

final readonly class TildePartialConstraint extends PartialConstraint
{
    // ~1.2
    // --- to ---
    // >=1.2.0 , <2.0.0

    protected function __construct(
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

    public static function transform(string $input): GroupConstraint
    {
        $partial = TildePartialConstraint::fromString($input);

        $first = $partial->minimum();
        $second = $partial->maximum();

        return new GroupConstraint(
            first: $first,
            second: $second,
            operator: Operator::And,
        );
    }

    // Internals

    protected function minimum(): SingleConstraint
    {
        return new SingleConstraint(
            type: SingleConstraintType::GreaterThanOrEqualTo,
            major: $this->major,
            minor: $this->minor ?? 0,
            patch: $this->patch ?? 0,
        );
    }

    protected function maximum(): SingleConstraint
    {
        $major = $this->major;
        $minor = $this->minor;
        $patch = $this->patch;

        if($major === 0) {
            return new SingleConstraint(
                type: SingleConstraintType::LessThan,
                major: 0,
                minor: $minor + 1,
                patch: 0,
            );
        }

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
