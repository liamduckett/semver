<?php

namespace App\Models;

readonly class ConstraintGroup
{
    /** @var list<Constraint>  */
    public array $constraints;

    public function __construct(string $version)
    {
        $this->constraints = [new Constraint($version)];
    }

    public function allows(Version $version): bool
    {
        $result = true;

        foreach($this->constraints as $constraint) {
            $result &= $constraint->allows($version);
        }

        return $result;
    }
}
