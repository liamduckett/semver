<?php

namespace App\Models;

use App\Enums\ConstraintType;

readonly class Constraint
{
    public ConstraintType $type;
    public int $major;
    public int $minor;
    public int $patch;

    public function __construct(string $version)
    {
        $versionParts = ltrim($version, '>=');
        $versionParts = explode('.', $versionParts);

        $this->type = $this->determineType($version);
        [$this->major, $this->minor, $this->patch] = $versionParts;
    }

    protected function determineType(string $version): ConstraintType
    {
        $start = substr($version, 0, 2);

        return match(true) {
            $start === '>=' => ConstraintType::RangeGreaterThanOrEqualTo,
            $start[0] === '>' => ConstraintType::RangeGreaterThan,
            true => ConstraintType::Exact,
        };
    }

    public function allows(Version $version): bool
    {
        if($this->type === ConstraintType::RangeGreaterThanOrEqualTo) {
            return $version->major >= $this->major
                && $version->minor >= $this->minor
                && $version->patch >= $this->patch;
        }

        if($this->type === ConstraintType::RangeGreaterThan) {
            if($version->major > $this->major) {
                return true;
            }

            if($version->major === $this->major
                && $version->minor > $this->minor) {
                return true;
            }

            if($version->major === $this->major
                && $version->minor === $this->minor
                && $version->patch > $this->patch) {
                return true;
            }

            return false;
        }

        return $version->major === $this->major
            && $version->minor === $this->minor
            && $version->patch === $this->patch;
    }
}
