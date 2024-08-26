<?php

namespace App\Models;

readonly class Version
{
    public int $major;
    public int $minor;
    public int $patch;

    public function __construct(string $version)
    {
        $versionParts = explode('.', $version);

        [$this->major, $this->minor, $this->patch] = $versionParts;
    }
}
