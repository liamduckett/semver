<?php

namespace App\Enums;

use App\Models\SingleConstraint;
use App\Models\Version;

enum SingleConstraintType
{
    case Exact;
    case Not;
    case GreaterThan;
    case LessThan;
    case GreaterThanOrEqualTo;
    case LessThanOrEqualTo;

    public static function determine(string $constraint): self
    {
        return match(true) {
            str_starts_with($constraint, '<=') => self::LessThanOrEqualTo,
            str_starts_with($constraint, '<') => self::LessThan,
            str_starts_with($constraint, '>=') => self::GreaterThanOrEqualTo,
            str_starts_with($constraint, '>') => self::GreaterThan,
            str_starts_with($constraint, '!=') => self::Not,
            true => self::Exact,
        };
    }

    public function allows(SingleConstraint $constraint, Version $version): bool
    {
        return match($this) {
            self::LessThanOrEqualTo => $this->isLessThan($constraint, $version) || $this->isEqualTo($constraint, $version),
            self::LessThan => $this->isLessThan($constraint, $version),
            self::GreaterThanOrEqualTo => $this->isGreaterThan($constraint, $version) || $this->isEqualTo($constraint, $version),
            self::GreaterThan => $this->isGreaterThan($constraint, $version),
            self::Not => $this->isNotEqualTo($constraint, $version),
            self::Exact => $this->isEqualTo($constraint, $version),
        };
    }

    // Internals

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
