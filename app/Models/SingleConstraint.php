<?php

namespace App\Models;

use App\Enums\SingleConstraintType;

readonly class SingleConstraint extends Constraint
{
    public SingleConstraintType $type;
    public int $major;
    public int $minor;
    public int $patch;

    public function __construct(string $constraint)
    {
        $version = ltrim($constraint, '=<>!');
        $versionParts = explode('.', $version);

        $this->type = SingleConstraintType::determine($constraint);
        dump($versionParts);
        [$this->major, $this->minor, $this->patch] = $versionParts;
    }

    public function allows(Version $version): bool
    {
        return $this->type->allows($this, $version);
    }
}
