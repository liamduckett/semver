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
    case HyphenatedRange;
    case WildcardRange;

    public static function determine(string $constraint): self
    {
        $start = substr($constraint, 0, 2);

        return match(true) {
            str_contains($constraint, '-') => self::HyphenatedRange,
            str_contains($constraint, '*') => self::WildcardRange,
            $start === '<=' => self::LessThanOrEqualTo,
            $start[0] === '<' => self::LessThan,
            $start === '>=' => self::GreaterThanOrEqualTo,
            $start[0] === '>' => self::GreaterThan,
            $start === '!=' => self::Not,
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

    public function requiresMajorMinorPatch(): bool
    {
        return match($this) {
            self::LessThanOrEqualTo => false,
            self::LessThan => false,
            self::GreaterThanOrEqualTo => false,
            self::GreaterThan => false,
            self::Not => true,
            self::Exact => true,
        };
    }

    // Internal

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
