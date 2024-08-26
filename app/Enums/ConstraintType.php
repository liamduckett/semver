<?php

namespace App\Enums;

enum ConstraintType
{
    case Exact;
    case RangeGreaterThan;
    case RangeGreaterThanOrEqualTo;
}
