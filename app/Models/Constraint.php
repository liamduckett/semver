<?php

namespace App\Models;

readonly abstract class Constraint
{
    public GroupConstraint|SingleConstraint $value;

    public static function create(string $input): self
    {
        $isGroup = str_contains($input, ',') || str_contains($input, '||');

        return $isGroup
            ? new GroupConstraint($input)
            : new SingleConstraint($input);
    }

    public function allows(Version $version): bool
    {
        return $this->value->allows($version);
    }
}
