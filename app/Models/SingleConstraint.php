<?php

namespace App\Models;

use App\Enums\SingleConstraintType;

readonly class SingleConstraint extends Constraint
{
    public function __construct(
        public SingleConstraintType $type,
        public int $major,
        public int $minor,
        public int $patch,
    ) {}

    public static function fromString(string $input): self
    {
        $version = ltrim($input, '=<>!');
        $versionParts = explode('.', $version);
        $versionParts = array_pad($versionParts, 3, 0);

        $type = SingleConstraintType::determine($input);
        [$major, $minor, $patch] = $versionParts;

        return new self(
            type: $type,
            major: $major,
            minor: $minor,
            patch: $patch,
        );
    }

    public function allows(Version $version): bool
    {
        return $this->type->allows($this, $version);
    }

    // Modifications

    public function changeType(SingleConstraintType $type): self
    {
        return new self(
            type: $type,
            major: $this->major,
            minor: $this->minor,
            patch: $this->patch,
        );
    }

    public function incrementLeastSignificant(): self
    {
        return new self(
            type: $this->type,
            major: $this->major,
            minor: $this->minor,
            patch: $this->patch + 1,
        );
    }
}
