<?php

namespace App\Models;

use App\Enums\Operator;
use App\Enums\SingleConstraintType;

readonly abstract class Constraint
{
    public static function create(string $input): self
    {
        $input = str_replace(' ', '', $input);

        $isGroup = str_contains($input, ',') || str_contains($input, '||');
        $needsTransform = str_contains($input, '-') || str_contains($input, '*') || str_starts_with($input, '~');

        return match(true) {
            $isGroup => GroupConstraint::fromString($input),
            $needsTransform => self::handleTransforms($input),
            true => SingleConstraint::fromString($input),
        };
    }

    abstract public static function fromString(string $input): self;

    abstract public function allows(Version $version): bool;

    // Internals

    protected static function handleTransforms(string $input): self
    {
        return match(true) {
            str_contains($input, '-') => self::handleHyphenatedRangeTransform($input),
            str_contains($input, '*') => self::handleWildcardRangeTransform($input),
            str_starts_with($input, '~') => self::handleTildeRangeTransform($input),
        };
    }

    protected static function handleHyphenatedRangeTransform(string $input): self
    {
        [$first, $second] = explode('-', $input);

        // 1.0.0 - 2.0.0
        // >=1.0.0 , <2.0.1

        $first = HyphenatedPartialConstraint::fromString($first)->minimum();
        $second = HyphenatedPartialConstraint::fromString($second)->maximum();

        return new GroupConstraint(
            first: $first,
            second: $second,
            operator: Operator::And,
        );
    }

    protected static function handleWildcardRangeTransform(string $input): self
    {
        // 1.0.*
        // >=1.0.0 , <1.1.0

        //  *
        // >= 0.0.0

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

    protected static function handleTildeRangeTransform(string $input): self
    {
        // ~1.2
        // >=1.2 <2.0.0

        $partial = PartialConstraint::fromString($input);

        $first = $partial->minimum();
        $second = $partial->maximum();

        return new GroupConstraint(
            first: $first,
            second: $second,
            operator: Operator::And,
        );
    }
}
