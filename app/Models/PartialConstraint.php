<?php

namespace App\Models;

use App\Enums\SingleConstraintType;

readonly class PartialConstraint
{
    public function __construct(
        public SingleConstraintType $type,
        public Wildcard|int $major,
        public Wildcard|int|null $minor,
        public Wildcard|int|null $patch,
    ) {}

    public static function fromString(string $input): self
    {
        $versionParts = explode('.', $input);
        $versionParts = array_pad($versionParts, 3, null);

        $type = SingleConstraintType::determine($input);
        [$major, $minor, $patch] = self::convertWildcards($versionParts);

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

        if($this->patch instanceof Wildcard) {
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
