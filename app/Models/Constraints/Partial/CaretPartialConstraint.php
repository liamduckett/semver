<?php

namespace App\Models\Constraints\Partial;

use App\Enums\Operator;
use App\Enums\SingleConstraintType;
use App\Models\Constraints\GroupConstraint;
use App\Models\Constraints\PartialConstraint;
use App\Models\Constraints\SingleConstraint;

final readonly class CaretPartialConstraint extends PartialConstraint
{
    // ^1.2.3
    // --- to ---
    // >=1.2.3 , <2.0.0

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
        $partial = CaretPartialConstraint::fromString($input);

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
        return new SingleConstraint(
            type: SingleConstraintType::LessThan,
            major: $this->major + 1,
            minor: 0,
            patch: 0,
        );
    }
}
