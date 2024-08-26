<?php

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;
use Illuminate\Foundation\Testing\TestCase;

class SemverTest extends TestCase
{
    #[Test]
    public function can_run_command(): void
    {
        $this->artisan('semver:check 8.0.0 7.0.0')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_missing_patch(): void
    {
        $this->artisan('semver:check 8.0 7.0.0')
            ->expectsOutput("constraint must be in the format MAJOR.MINOR.PATCH")
            ->assertExitCode(Command::FAILURE);
    }

    #[Test]
    public function rejects_non_integer_patch(): void
    {
        $this->artisan('semver:check 8.0.foo 7.0.0')
            ->expectsOutput("constraint MAJOR, MINOR and PATCH must all be integers")
            ->assertExitCode(Command::FAILURE);
    }
}
