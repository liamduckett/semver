<?php

namespace App\Rules;

use App\Enums\SingleConstraintType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class IsConstraint implements ValidationRule
{
    protected Closure $fail;

    /**
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // To allow calling from internal methods...
        $this->fail = $fail;

        $constraints = $this->split($value);

        foreach($constraints as $constraint) {
            $type = SingleConstraintType::determine($constraint);

            match(true) {
                $type === SingleConstraintType::HyphenatedRange => $this->validateHyphenatedRangeConstraint($constraint),
                $type->requiresMajorMinorPatch() => $this->validateExactConstraint($constraint),
                true => $this->validateInexactConstraint($constraint),
            };
        }
    }

    // Internal

    protected function fail(string $message): void
    {
        ($this->fail)($message);
    }

    protected function split(string $constraint): array
    {
        // don't allow any @ - we use this internally...
        if(str_contains($constraint, '@')) {
            $this->fail('Invalid character detected');
        }

        // remove spaces
        $constraint = str_replace(' ', '', $constraint);

        // convert special characters to @
        $constraint = str_replace(',', '@', $constraint);
        $constraint = str_replace('||', '@', $constraint);

        return explode('@', $constraint);
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

    protected function validateHyphenatedRangeConstraint(string $constraint): void
    {
        $constraints = explode('-', $constraint, 2);

        foreach($constraints as $constraint) {
            $this->validateInexactConstraint($constraint);
        }
    }
    protected function validateInexactConstraint(string $constraint): void
    {
        $semVerParts = $this->getSemVerParts($constraint);

        if(count($semVerParts) < 1) {
            $this->fail("Range constraint '$constraint' must specify at least MAJOR");
        }

        foreach($semVerParts as $semverPart) {
            $this->validateInteger($semverPart);
        }
    }

    protected function validateExactConstraint(string $constraint): void
    {
        $semVerParts = $this->getSemVerParts($constraint);

        if(count($semVerParts) !== 3) {
            $this->fail("Exact constraint '$constraint' must specify MAJOR, MINOR and PATCH");
        }

        foreach($semVerParts as $semverPart) {
            $this->validateInteger($semverPart);
        }
    }

    protected function validateInteger(string $integer): void
    {
        if($this->invalidInteger($integer)) {
            $this->fail("Constraint part '$integer' is not an integer");
        }
    }
}
