<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class IsConstraint implements ValidationRule
{
    /**
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $constraints = explode(',', $value);

        foreach($constraints as $key => $constraint) {
            $semVerParts = $this->getSemVerParts($constraint);

            $constraintNumber = count($constraints) === 1
                ? ''
                : $key + 1 . ' ';

            if(count($semVerParts) !== 3) {
                $fail('Constraint ' . $constraintNumber . 'must be in the format MAJOR.MINOR.PATCH');
            }

            foreach($semVerParts as $semverPart) {
                if($this->invalidInteger($semverPart)) {
                    $fail('Constraint ' . $constraintNumber . 'MAJOR, MINOR and PATCH must all be integers');
                }
            }
        }
    }

    protected function getSemVerParts(string $value): array
    {
        if(str_starts_with($value, '>=')) {
            $value = substr($value, 2);
        }

        elseif(str_starts_with($value, '>')) {
            $value = substr($value, 1);
        }

        elseif(str_starts_with($value, '<=')) {
            $value = substr($value, 2);
        }

        elseif(str_starts_with($value, '<')) {
            $value = substr($value, 1);
        }

        elseif(str_starts_with($value, '!=')) {
            $value = substr($value, 2);
        }

        return explode('.', $value);
    }

    protected function invalidInteger(string $str): bool
    {
        // ^            (Start of String)
        // (0|[1-9]\d*) (Match 0 or a Number without Leading Zeros)
        // \d*          (Zero or More Digits Following):
        // $            (End of String):
        return preg_match('/^(0|[1-9]\d*)$/', $str) === 0;
    }
}
