<?php

namespace App;

readonly class Constraint
{
    public int $major;
    public int $minor;
    public int $patch;

    public function __construct(string $version)
    {
        /** @var list<int> $versionParts */
        $versionParts = explode('.', $version);

        [$this->major, $this->minor, $this->patch] = $versionParts;
    }

    public function allows(Version $version): bool
    {
        return $this->major === $version->major
            && $this->minor === $version->minor
            && $this->patch === $version->patch;
    }
}
