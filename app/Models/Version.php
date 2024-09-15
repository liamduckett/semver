<?php

namespace App\Models;

final readonly class Version
{
    protected function __construct(
        public int $major,
        public int $minor,
        public int $patch,
    ) {}

    public static function fromString(string $input): self
    {
        $versionParts = explode('.', $input);

        $versionParts = array_map(
            fn(string $versionPart) => (int) $versionPart,
            $versionParts,
        );

        [$major, $minor, $patch] = $versionParts;

        return new self(
            major: $major,
            minor: $minor,
            patch: $patch,
        );
    }
}
