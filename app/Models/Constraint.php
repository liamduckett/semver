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
        $versionParts = ltrim($version, '>');
        $versionParts = explode('.', $versionParts);

        $this->type = $this->determineType($version);
        [$this->major, $this->minor, $this->patch] = $versionParts;
    }

    protected function determineType(string $version): ConstraintType
    {
        return str_starts_with($version, '>') ? ConstraintType::Range : ConstraintType::Exact;
    }

    public function allows(Version $version): bool
    {
        if($this->type === ConstraintType::Range) {
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

        return $this->major === $version->major
            && $this->minor === $version->minor
            && $this->patch === $version->patch;
    }
}
