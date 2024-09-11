<?php

namespace App\Models;

use App\Enums\Operator;

readonly class GroupConstraint extends Constraint
{
    public SingleConstraint $first;
    public SingleConstraint $second;
    public Operator $operator;

    public function __construct(string $input)
    {
        $operator = str_contains($input, ',')
            ? Operator::And
            : Operator::Or;

        $constraints = explode($operator->value, $input, 2);

        $this->first = new SingleConstraint($constraints[0]);
        $this->second = new SingleConstraint($constraints[1]);
        $this->operator = $operator;
    }

    public function allows(Version $version): bool
    {
        return $this->operator->allows($version, $this);
    }
}
