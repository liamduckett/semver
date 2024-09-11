<?php

namespace App\Enums;

use App\Models\SingleConstraint;
use App\Models\Version;

enum SingleConstraintType
{
    case Exact;
    case Not;
    case RangeGreaterThan;
    case RangeLessThan;
    case RangeGreaterThanOrEqualTo;
    case RangeLessThanOrEqualTo;

    public static function determine(string $constraint): self
    {
        $start = substr($constraint, 0, 2);

        return match(true) {
            $start === '<=' => self::RangeLessThanOrEqualTo,
            $start[0] === '<' => self::RangeLessThan,
            $start === '>=' => self::RangeGreaterThanOrEqualTo,
            $start[0] === '>' => self::RangeGreaterThan,
            $start === '!=' => self::Not,
            true => self::Exact,
        };
    }

    public function allows(SingleConstraint $constraint, Version $version): bool
    {
        if($this === SingleConstraintType::RangeLessThanOrEqualTo) {
            return $version->major <= $constraint->major
                && $version->minor <= $constraint->minor
                && $version->patch <= $constraint->patch;
        }

        if($this === SingleConstraintType::RangeLessThan) {
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

        if($this === SingleConstraintType::RangeGreaterThanOrEqualTo) {
            return $version->major >= $constraint->major
                && $version->minor >= $constraint->minor
                && $version->patch >= $constraint->patch;
        }

        if($this === SingleConstraintType::RangeGreaterThan) {
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

        if($this === SingleConstraintType::Not) {
            return $version->major !== $constraint->major
                || $version->minor !== $constraint->minor
                || $version->patch !== $constraint->patch;
        }

        return $version->major === $constraint->major
            && $version->minor === $constraint->minor
            && $version->patch === $constraint->patch;
    }
}
