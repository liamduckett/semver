<?php

namespace App\Models;

use App\Models\Constraints\GroupConstraint;
use App\Models\Constraints\Partial\CaretPartialConstraint;
use App\Models\Constraints\Partial\HyphenatedPartialConstraint;
use App\Models\Constraints\Partial\TildePartialConstraint;
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
            str_contains($input, '-') => HyphenatedPartialConstraint::transform($input),
            str_contains($input, '*') => WildcardPartialConstraint::transform($input),
            str_starts_with($input, '~') => TildePartialConstraint::transform($input),
            str_starts_with($input, '^') => CaretPartialConstraint::transform($input),
            default => new LogicException("Untransformable Constraint passed to Constraint Transformer"),
        };
    }
}
