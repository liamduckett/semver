<?php

namespace App\Models;

use App\Enums\Operator;
use App\Enums\SingleConstraintType;
use App\Models\Constraints\GroupConstraint;
use App\Models\Constraints\Partial\CaretPartialConstraint;
use App\Models\Constraints\Partial\HyphenatedPartialConstraint;
use App\Models\Constraints\Partial\TildePartialConstraint;
use App\Models\Constraints\Partial\Wildcard;
use App\Models\Constraints\Partial\WildcardPartialConstraint;
use App\Models\Constraints\SingleConstraint;
use LogicException;

readonly abstract class Constraint
{
    public static function create(string $input): self
    {
        $input = str_replace(' ', '', $input);

        return match(true) {
            self::isGroup($input) => GroupConstraint::fromString($input),
            self::needsTransform($input) => self::handleTransforms($input),
            true => SingleConstraint::fromString($input),
        };
    }

    abstract public static function fromString(string $input): self;

    abstract public function allows(Version $version): bool;

    // Internals

    protected static function isGroup(string $input): bool
    {
        return str_contains($input, ',') || str_contains($input, '||');
    }

    protected static function needsTransform(string $input): bool
    {
        return str_contains($input, '-')
            || str_contains($input, '*')
            || str_starts_with($input, '~')
            || str_starts_with($input, '^');
    }

    protected static function handleTransforms(string $input): self
    {
        return match(true) {
            str_contains($input, '-') => self::handleHyphenatedRangeTransform($input),
            str_contains($input, '*') => self::handleWildcardRangeTransform($input),
            str_starts_with($input, '~') => self::handleTildeRangeTransform($input),
            str_starts_with($input, '^') => self::handleCaretRangeTransform($input),
            default => new LogicException("Untransformable Constraint passed to Constraint Transformer"),
        };
    }

    protected static function handleHyphenatedRangeTransform(string $input): self
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

    protected static function handleWildcardRangeTransform(string $input): self
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

    protected static function handleTildeRangeTransform(string $input): self
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

    protected static function handleCaretRangeTransform(string $input): self
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
}
