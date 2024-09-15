<?php

namespace App\Rules;

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
            match(true) {
                str_contains($constraint, '-') => $this->validateHyphenatedRangeConstraint($constraint),
                str_contains($constraint, '*') => $this->validatesWildcardRangeConstraint($constraint),
                str_starts_with($constraint, '~') => $this->validateInexactConstraint($constraint),
                str_starts_with($constraint, '^') => $this->validateInexactConstraint($constraint),
                str_starts_with($constraint, '<') || str_starts_with($constraint, '>') => $this->validateInexactConstraint($constraint),
                true => $this->validateExactConstraint($constraint),
            };
        }
    }

    // Internals

    protected function fail(string $message): void
    {
        ($this->fail)($message);
    }

    /**
     * @param string $constraint
     * @return list<string>
     */
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

    /**
     * @param string $value
     * @return list<string>
     */
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

        elseif(str_starts_with($value, '~')) {
            $value = substr($value, 1);
        }

        elseif(str_starts_with($value, '^')) {
            $value = substr($value, 1);
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
            $this->fail("Inexact constraint '$constraint' must specify at least MAJOR");
        }

        foreach($semVerParts as $semVerPart) {
            $this->validateInteger($semVerPart);
        }
    }

    protected function validateExactConstraint(string $constraint): void
    {
        $semVerParts = $this->getSemVerParts($constraint);

        if(count($semVerParts) !== 3) {
            $this->fail("Exact constraint '$constraint' must specify MAJOR, MINOR and PATCH");
        }

        foreach($semVerParts as $semVerPart) {
            $this->validateInteger($semVerPart);
        }
    }

    protected function validatesWildcardRangeConstraint(string $constraint): void
    {
        $semVerParts = explode('.', $constraint);

        $asterisks = array_filter(
            $semVerParts,
            fn(string $semVerPart) => $semVerPart === '*',
        );

        if(count($asterisks) !== 1) {
            $this->fail("Wildcard range constraint '$constraint' must have one asterisk");
        }

        $nonAsteriskSemVerParts = array_filter(
            $semVerParts,
            fn(string $semVerPart) => $semVerPart !== '*',
        );

        foreach($nonAsteriskSemVerParts as $semVerPart) {
            $this->validateInteger($semVerPart);
        }

        if(strpos($constraint, '*') + 1 !== strlen($constraint)) {
            $this->fail("Wildcard range constraint '$constraint' must end in an asterisk");
        }
    }

    protected function validateInteger(string $integer): void
    {
        if($this->invalidInteger($integer)) {
            $this->fail("Constraint part '$integer' is not an integer");
        }
    }
}
