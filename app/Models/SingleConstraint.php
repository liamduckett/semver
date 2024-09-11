<?php

namespace App\Models;

use App\Enums\SingleConstraintType;

readonly class SingleConstraint
{
    public SingleConstraintType $type;
    public int $major;
    public int $minor;
    public int $patch;

    public function __construct(string $version)
    {
        $versionParts = ltrim($version, '=<>!');
        $versionParts = explode('.', $versionParts);

        $this->type = $this->determineType($version);
        [$this->major, $this->minor, $this->patch] = $versionParts;
    }

    protected function determineType(string $version): SingleConstraintType
    {
        $start = substr($version, 0, 2);

        return match(true) {
            $start === '<=' => SingleConstraintType::RangeLessThanOrEqualTo,
            $start[0] === '<' => SingleConstraintType::RangeLessThan,
            $start === '>=' => SingleConstraintType::RangeGreaterThanOrEqualTo,
            $start[0] === '>' => SingleConstraintType::RangeGreaterThan,
            $start === '!=' => SingleConstraintType::Not,
            true => SingleConstraintType::Exact,
        };
    }

    public function allows(Version $version): bool
    {
        return $this->type->allows($this, $version);
    }
}
