<?php

namespace App\Models\Constraints\Partial;

use App\Enums\SingleConstraintType;
use App\Models\Constraints\SingleConstraint;

readonly class WildcardPartialConstraint extends PartialConstraint
{
    // 1.0.*
    // --- to ---
    // >=1.0.0 , <1.1.0

    //  *
    // --- to ---
    // >= 0.0.0

    public function __construct(
        public Wildcard|int         $major,
        public Wildcard|int|null    $minor,
        public Wildcard|int|null    $patch,
    ) {}

    public static function fromString(string $input): self
    {
        $versionParts = explode('.', $input);
        $versionParts = array_pad($versionParts, 3, null);
        [$major, $minor, $patch] = self::convertWildcards($versionParts);

        return new self(
            major: $major,
            minor: $minor,
            patch: $patch,
        );
    }

    public function minimum(): SingleConstraint
    {
        $minor = $this->minor instanceof Wildcard
            ? 0
            : $this->minor;

        $patch = $this->patch instanceof Wildcard
            ? 0
            : $this->patch;

        return new SingleConstraint(
            type: SingleConstraintType::GreaterThanOrEqualTo,
            major: $this->major,
            minor: $minor ?? 0,
            patch: $patch ?? 0,
        );
    }

    public function maximum(): SingleConstraint
    {
        $major = $this->major;
        $minor = $this->minor;

        if ($this->patch instanceof Wildcard) {
            $minor += 1;
        } else {
            $major += 1;
            $minor = 0;
        }

        return new SingleConstraint(
            type: SingleConstraintType::LessThan,
            major: $major,
            minor: $minor,
            patch: 0,
        );
    }

    // Internals

    protected static function convertWildcards(array $versionParts): array
    {
        return array_map(
            fn(?string $versionPart) => $versionPart === '*' ? new Wildcard : $versionPart,
            $versionParts,
        );
    }
}
