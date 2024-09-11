<?php

namespace App\Models;

readonly class Constraint
{
    public GroupConstraint|SingleConstraint $value;

    public function __construct(string $input)
    {
        $isGroup = str_contains($input, ',') || str_contains($input, '||');

        $this->value = $isGroup
            ? new GroupConstraint($input)
            : new SingleConstraint($input);
    }

    public function allows(Version $version): bool
    {
        return $this->value->allows($version);
    }
}
