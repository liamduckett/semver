<?php

namespace App\Rules;

use App\Enums\SingleConstraintType;
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
        // don't allow any @ - we use this internally...
        if(str_contains($value, '@')) {
            $fail('Invalid character detected');
        }

        $replacedValue = str_replace(',', '@', $value);
        $replacedValue = str_replace('||', '@', $replacedValue);

        $constraints = explode('@', $replacedValue);

        foreach($constraints as $key => $constraint) {
            $semVerParts = $this->getSemVerParts($constraint);

            $type = SingleConstraintType::determine($constraint);

            if($type->requiresMajorMinorPatch() && count($semVerParts) !== 3) {
                $fail("Exact constraint '$constraint' must specify MAJOR, MINOR and PATCH");
            }
            elseif(count($semVerParts) < 2) {
                $fail("Range constraint '$constraint' must specify at least MAJOR and MINOR");
            }

            foreach($semVerParts as $semverPart) {
                if($this->invalidInteger($semverPart)) {
                    $fail("Constraint part '$semverPart' is not an integer");
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
