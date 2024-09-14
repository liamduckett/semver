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

        $needsTransform = str_contains($input, '-') || str_contains($input, '*');

        return match(true) {
            $isGroup => GroupConstraint::fromString($input),
            $needsTransform => self::handleTransforms($input),
            true => SingleConstraint::fromString($input),
        };
    }

    protected static function handleTransforms(string $input): self
    {
        return match(true) {
            str_contains($input, '-') => self::handleHyphenatedRangeTransform($input),
            str_contains($input, '*') => self::handleWildcardRangeTransform($input),
        };
    }

    public static function handleHyphenatedRangeTransform(string $input): self
    {
        [$first, $second] = explode('-', $input);

        // 1.0.0 - 2.0.0
        //    vvv
        // >=1.0.0 , <2.0.1

        $first = PartialConstraint::fromString($first)->changeType(SingleConstraintType::GreaterThanOrEqualTo);
        $second = PartialConstraint::fromString($second)->changeType(SingleConstraintType::LessThan);

        $second = $second->incrementLeastSignificant();

        return new GroupConstraint(
            first: $first->toSingleConstraint(),
            second: $second->toSingleConstraint(),
            operator: Operator::And,
        );
    }

    public static function handleWildcardRangeTransform(string $input): self
    {
        //       1.0.*
        //        vvv
        // >=1.0.0 , <1.1.0

        //  *
        // vvv
        // >= 0.0.0

        $partial = PartialConstraint::fromString($input);

        if($partial->major instanceof Wildcard) {
            return new SingleConstraint(
                type: SingleConstraintType::GreaterThanOrEqualTo,
                major: 0,
                minor: 0,
                patch: 0,
            );
        }

        $first = $partial
            ->changeType(SingleConstraintType::GreaterThanOrEqualTo)
            ->minimum();

        $second = $partial
            ->changeType(SingleConstraintType::LessThan)
            ->maximum();

        return new GroupConstraint(
            first: $first,
            second: $second,
            operator: Operator::And,
        );
    }

    abstract public static function fromString(string $input): self;

    abstract public function allows(Version $version): bool;
}
