<?php

namespace App\Models\Constraints;

use App\Models\Constraint;

readonly abstract class PartialConstraint
{
    abstract public static function transform(string $input): Constraint;
}

