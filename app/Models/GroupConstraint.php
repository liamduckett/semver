<?php

namespace App\Models;

readonly class GroupConstraint
{
    public SingleConstraint $first;
    public SingleConstraint $second;

    public function __construct(string $input)
    {
        $constraints = explode(',', $input, 2);

        $this->first = new SingleConstraint($constraints[0]);
        $this->second = new SingleConstraint($constraints[1]);
    }

    public function allows(Version $version): bool
    {
        return $this->first->allows($version) && $this->second->allows($version);
    }
}
