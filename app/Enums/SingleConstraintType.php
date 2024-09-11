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
        return match($this) {
            self::RangeLessThanOrEqualTo => $this->isLessThan($constraint, $version) || $this->isEqualTo($constraint, $version),
            self::RangeLessThan => $this->isLessThan($constraint, $version),
            self::RangeGreaterThanOrEqualTo => $this->isGreaterThan($constraint, $version) || $this->isEqualTo($constraint, $version),
            self::RangeGreaterThan => $this->isGreaterThan($constraint, $version),
            self::Not => $this->isNotEqualTo($constraint, $version),
            self::Exact => $this->isEqualTo($constraint, $version),
        };
    }

    protected function isEqualTo(SingleConstraint $constraint, Version $version): bool
    {
        return $version->major === $constraint->major
            && $version->minor === $constraint->minor
            && $version->patch === $constraint->patch;
    }

    protected function isNotEqualTo(SingleConstraint $constraint, Version $version): bool
    {
        return ! $this->isEqualTo($constraint, $version);
    }

    protected function isLessThan(SingleConstraint $constraint, Version $version): bool
    {
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

        return false;
    }

    protected function isGreaterThan(SingleConstraint $constraint, Version $version): bool
    {
        return ! $this->isLessThan($constraint, $version) && $this->isNotEqualTo($constraint, $version);
    }
}
