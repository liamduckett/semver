<?php

namespace App\Enums;

use App\Models\Constraint;
use App\Models\Version;

enum ConstraintType
{
    case Exact;
    case RangeGreaterThan;
    case RangeLessThan;
    case RangeGreaterThanOrEqualTo;
    case RangeLessThanOrEqualTo;

    public function allows(Constraint $constraint, Version $version): bool
    {
        if($this === ConstraintType::RangeLessThanOrEqualTo) {
            return $version->major <= $constraint->major
                && $version->minor <= $constraint->minor
                && $version->patch <= $constraint->patch;
        }

        if($this === ConstraintType::RangeLessThan) {
            if($version->major < $constraint->major) {
                return true;
            }

            if($version->major === $constraint->major
                && $version->minor < $constraint->minor) {
                return true;
            }

            if($version->major === $constraint->major
                && $version->minor === $constraint->minor) {
                return $version->patch < $constraint->patch;
            }
        }

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
                && $version->minor === $constraint->minor) {
                return $version->patch > $constraint->patch;
            }
        }

        return $version->major === $constraint->major
            && $version->minor === $constraint->minor
            && $version->patch === $constraint->patch;
    }
}
