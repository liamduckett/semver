<?php

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;
use Illuminate\Foundation\Testing\TestCase;

class SemVerTest extends TestCase
{
    #[Test]
    public function can_run_command(): void
    {
        $this->artisan('semver:check 8.0.0 8.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_range_greater_than_with_two_symbols(): void
    {
        $this->artisan('semver:check ">>7.0.0" 7.0.1')
            ->expectsOutput("Constraint part '>7' is not an integer")
            ->assertExitCode(Command::FAILURE);
    }

    #[Test]
    public function rejects_missing_patch(): void
    {
        $this->artisan('semver:check 8.0 7.0.0')
            ->expectsOutput("Exact constraint '8.0' must specify MAJOR, MINOR and PATCH")
            ->assertExitCode(Command::FAILURE);
    }

    #[Test]
    public function rejects_non_integer_patch(): void
    {
        $this->artisan('semver:check 8.0.foo 7.0.0')
            ->expectsOutput("Constraint part 'foo' is not an integer")
            ->assertExitCode(Command::FAILURE);
    }

    #[Test]
    public function rejects_exact_constraint_without_patch(): void
    {
        $this->artisan('semver:check "7.0" 7.0.1')
            ->expectsOutput("Exact constraint '7.0' must specify MAJOR, MINOR and PATCH")
            ->assertExitCode(Command::FAILURE);
    }
}
