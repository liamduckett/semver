<?php

namespace App\Enums;

use App\Models\Constraint;
use App\Models\Version;

enum ConstraintType
{
    case Exact;
    case RangeGreaterThan;
    case RangeGreaterThanOrEqualTo;

    public function allows(Constraint $constraint, Version $version): bool
    {
        if($this === ConstraintType::RangeGreaterThanOrEqualTo) {
            return $version->major >= $constraint->major
                && $version->minor >= $constraint->minor
                && $version->patch >= $constraint->patch;
        }

        if($this === ConstraintType::RangeGreaterThan) {
            if($version->major > $constraint->major) {
                return true;
            }

            if($version->major === $constraint->major
                && $version->minor > $constraint->minor) {
                return true;
            }

            if($version->major === $constraint->major
                && $version->minor === $constraint->minor
                && $version->patch > $constraint->patch) {
                return true;
            }

            return false;
        }

        return $version->major === $constraint->major
            && $version->minor === $constraint->minor
            && $version->patch === $constraint->patch;
    }
}
