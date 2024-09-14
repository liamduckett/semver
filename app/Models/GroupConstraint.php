<?php

namespace App\Models;

use App\Enums\Operator;

readonly class GroupConstraint extends Constraint
{
    public function __construct(
        public Constraint $first,
        public Constraint $second,
        public Operator $operator,
    ) {}

    public static function fromString(string $input): self
    {
        $operator = str_contains($input, '||')
            ? Operator::Or
            : Operator::And;

        $constraints = explode($operator->value, $input, 2);

        $first = self::create($constraints[0]);
        $second = self::create($constraints[1]);

        return new self(
            first: $first,
            second: $second,
            operator: $operator,
        );
    }

    public function allows(Version $version): bool
    {
        return $this->operator->allows($version, $this);
    }
}
