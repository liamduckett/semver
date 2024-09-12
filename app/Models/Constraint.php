<?php

namespace App\Models;

use App\Enums\Operator;
use App\Enums\SingleConstraintType;

readonly abstract class Constraint
{
    public static function create(string $input): self
    {
        // if we need to convert from hyphenated range to Group of ranges...
        $isHyphenatedRange = str_contains($input, '-');

        if($isHyphenatedRange) {
            [$first, $second] = explode('-', $input);

            // 1.0.0 - 2.0.0
            //    vvv
            // >=1.0.0 , <2.0.1

            $first = SingleConstraint::fromString($first)->changeType(SingleConstraintType::RangeGreaterThanOrEqualTo);
            $second = SingleConstraint::fromString($second)->changeType(SingleConstraintType::RangeLessThan);

            $second = $second->incrementLeastSignificant();

            return new GroupConstraint(
                first: $first,
                second: $second,
                operator: Operator::And,
            );
        }

        $input = str_replace(' ', '', $input);
        $isGroup = str_contains($input, ',') || str_contains($input, '||');

        return $isGroup
            ? GroupConstraint::fromString($input)
            : SingleConstraint::fromString($input);
    }

    abstract public static function fromString(string $input): self;

    abstract public function allows(Version $version): bool;
}
