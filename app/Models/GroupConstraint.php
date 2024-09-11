<?php

namespace App\Models;

use App\Enums\Operator;

readonly class GroupConstraint extends Constraint
{
    public Constraint $first;
    public Constraint $second;
    public Operator $operator;

    public function __construct(string $input)
    {
        $operator = str_contains($input, ',')
            ? Operator::And
            : Operator::Or;

        $constraints = explode($operator->value, $input, 2);

        $this->first = self::create($constraints[0]);
        $this->second = self::create($constraints[1]);
        $this->operator = $operator;
    }

    public function allows(Version $version): bool
    {
        return $this->operator->allows($version, $this);
    }
}
