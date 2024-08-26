<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class IsVersion implements ValidationRule
{
    /**
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var string $value */

        $semverParts = explode('.', $value);

        if(count($semverParts) !== 3) {
            $fail("$attribute must be in the format MAJOR.MINOR.PATCH");
        }

        foreach($semverParts as $semverPart) {
            if($this->invalidInteger($semverPart)) {
                $fail("$attribute MAJOR, MINOR and PATCH must all be integers");
            }
        }
    }

    protected function invalidInteger(string $str): bool
    {
        // ^ (Start of String)
        // (0|[1-9]\d*) (Match 0 or a Number without Leading Zeros)
        // \d* (Zero or More Digits Following):
        // $ (End of String):
        return preg_match('/^(0|[1-9]\d*)$/', $str) === 0;
    }
}
