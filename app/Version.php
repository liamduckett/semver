<?php

namespace App;

readonly class Version
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
}
