<?php

namespace App\Models\Constraints\Partial;

use App\Enums\Operator;
use App\Enums\SingleConstraintType;
use App\Models\Constraints\GroupConstraint;
use App\Models\Constraints\PartialConstraint;
use App\Models\Constraints\SingleConstraint;

final readonly class HyphenatedPartialConstraint extends PartialConstraint
{
    // 1.0.0 - 2.0.0
    // --- to ---
    // >=1.0.0 , <2.0.1

    protected function __construct(
        public int $major,
        public int|null $minor,
        public int|null $patch,
    ) {}

    public static function fromString(string $input): self
    {
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
        [$first, $second] = explode('-', $input);

        $first = HyphenatedPartialConstraint::fromString($first)->minimum();
        $second = HyphenatedPartialConstraint::fromString($second)->maximum();

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

        match (true) {
            $patch !== null => $patch += 1,
            $minor !== null => $minor += 1,
            true => $major += 1,
        };

        return new SingleConstraint(
            type: SingleConstraintType::LessThan,
            major: $major,
            minor: $minor ?? 0,
            patch: $patch ?? 0,
        );
    }
}
