<?php

namespace App\Models;

readonly abstract class PartialConstraint
{
    public static function fromString(string $input): self
    {
        return match(true) {
            str_contains($input, '-') => HyphenatedPartialConstraint::fromString($input),
            str_contains($input, '*') => WildcardPartialConstraint::fromString($input),
        };
    }

    // Modifications

    abstract public function minimum(): SingleConstraint;

    abstract public function maximum(): SingleConstraint;
}

