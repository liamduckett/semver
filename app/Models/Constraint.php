<?php

namespace App\Models;

readonly abstract class Constraint
{
    public static function create(string $input): self
    {
        $input = str_replace(' ', '', $input);
        $isGroup = str_contains($input, ',') || str_contains($input, '||');

        return $isGroup
            ? new GroupConstraint($input)
            : new SingleConstraint($input);
    }

    abstract public function allows(Version $version): bool;
}
