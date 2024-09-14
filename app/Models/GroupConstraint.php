<?php

namespace App\Models;

use App\Enums\Operator;
use App\Enums\SingleConstraintType;

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

    public static function fromHyphenatedRangeString(string $input): self
    {
        [$first, $second] = explode('-', $input);

        // 1.0.0 - 2.0.0
        //    vvv
        // >=1.0.0 , <2.0.1

        $first = SingleConstraint::fromString($first)->changeType(SingleConstraintType::RangeGreaterThanOrEqualTo);
        $second = SingleConstraint::fromString($second)->changeType(SingleConstraintType::RangeLessThan);

        $second = $second->incrementLeastSignificant();

        return new self(
            first: $first,
            second: $second,
            operator: Operator::And,
        );
    }

    public function allows(Version $version): bool
    {
        return $this->operator->allows($version, $this);
    }
}
