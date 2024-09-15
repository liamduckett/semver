<?php

namespace App\Models\Constraints\Partial;

use App\Enums\Operator;
use App\Enums\SingleConstraintType;
use App\Models\Constraint;
use App\Models\Constraints\GroupConstraint;
use App\Models\Constraints\PartialConstraint;
use App\Models\Constraints\SingleConstraint;

final readonly class WildcardPartialConstraint extends PartialConstraint
{
    // 1.0.*
    // --- to ---
    // >=1.0.0 , <1.1.0

    //  *
    // --- to ---
    // >= 0.0.0

    protected function __construct(
        public Wildcard|int         $major,
        public Wildcard|int|null    $minor,
        public Wildcard|int|null    $patch,
    ) {}

    public static function fromString(string $input): self
    {
        $versionParts = explode('.', $input);
        $versionParts = array_pad($versionParts, 3, null);
        [$major, $minor, $patch] = self::convertWildcards($versionParts);

        return new self(
            major: $major,
            minor: $minor,
            patch: $patch,
        );
    }

    public static function transform(string $input): Constraint
    {
        $partial = WildcardPartialConstraint::fromString($input);

        if($partial->major instanceof Wildcard) {
            return new SingleConstraint(
                type: SingleConstraintType::GreaterThanOrEqualTo,
                major: 0,
                minor: 0,
                patch: 0,
            );
        }

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
        $minor = $this->minor instanceof Wildcard
            ? 0
            : $this->minor;

        $patch = $this->patch instanceof Wildcard
            ? 0
            : $this->patch;

        return new SingleConstraint(
            type: SingleConstraintType::GreaterThanOrEqualTo,
            major: $this->major,
            minor: $minor ?? 0,
            patch: $patch ?? 0,
        );
    }

    protected function maximum(): SingleConstraint
    {
        $major = $this->major;
        $minor = $this->minor;

        if ($this->patch instanceof Wildcard) {
            $minor += 1;
        } else {
            $major += 1;
            $minor = 0;
        }

        return new SingleConstraint(
            type: SingleConstraintType::LessThan,
            major: $major,
            minor: $minor,
            patch: 0,
        );
    }

    /**
     * @param list<string> $versionParts
     * @return list<Wildcard|string>
     */
    protected static function convertWildcards(array $versionParts): array
    {
        return array_map(
            fn(?string $versionPart) => $versionPart === '*' ? new Wildcard : $versionPart,
            $versionParts,
        );
    }
}
