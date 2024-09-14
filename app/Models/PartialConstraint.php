<?php

namespace App\Models;

use App\Enums\SingleConstraintType;

readonly class PartialConstraint
{
    public function __construct(
        public SingleConstraintType $type,
        public int $major,
        public ?int $minor,
        public ?int $patch,
    ) {}

    public static function fromString(string $input): self
    {
        $version = ltrim($input, '=<>!');
        $versionParts = explode('.', $version);
        $versionParts = array_pad($versionParts, 3, null);

        $type = SingleConstraintType::determine($input);
        [$major, $minor, $patch] = $versionParts;

        return new self(
            type: $type,
            major: $major,
            minor: $minor,
            patch: $patch,
        );
    }

    // Modifications

    public function toSingleConstraint(): SingleConstraint
    {
        return new SingleConstraint(
            type: $this->type,
            major: $this->major,
            minor: $this->minor ?? 0,
            patch: $this->patch ?? 0,
        );
    }

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
        $major = $this->major;
        $minor = $this->minor;
        $patch = $this->patch;

        match(true) {
            $patch !== null => $patch += 1,
            $minor !== null => $minor += 1,
            true => $major += 1,
        };

        return new self(
            type: $this->type,
            major: $major,
            minor: $minor,
            patch: $patch,
        );
    }
}
