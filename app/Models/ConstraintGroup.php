<?php

namespace App\Models;

readonly class ConstraintGroup
{
    /** @var list<Constraint>  */
    public array $constraints;

    public function __construct(string $input)
    {
        $constraints = explode(',', $input);

        $this->constraints = array_map(
            fn(string $constraint) => new Constraint($constraint),
            $constraints,
        );
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
