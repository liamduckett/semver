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
    public function returns_failure_for_exact_check(): void
    {
        $this->artisan('semver:check 8.0.0 8.0.1')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_not_check(): void
    {
        $this->artisan('semver:check "!=8.0.0" 8.0.1')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function returns_failure_for_not_check(): void
    {
        $this->artisan('semver:check "!=8.0.0" 8.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_range_greater_than_major(): void
    {
        $this->artisan('semver:check ">7.0.0" 8.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_range_greater_than_patch(): void
    {
        $this->artisan('semver:check ">7.0.0" 7.0.1')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_range_greater_than_equal(): void
    {
        $this->artisan('semver:check ">7.0.0" 7.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_range_greater_than_or_equal_to_major(): void
    {
        $this->artisan('semver:check ">=7.0.0" 8.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_range_greater_than_or_equal_to_patch(): void
    {
        $this->artisan('semver:check ">=7.0.0" 7.0.1')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_range_greater_than_or_equal_to_equal(): void
    {
        $this->artisan('semver:check ">=7.0.0" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_range_greater_than_or_equal_to_less(): void
    {
        $this->artisan('semver:check ">=7.0.0" 6.9.9')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_range_less_than_major(): void
    {
        $this->artisan('semver:check "<7.0.0" 6.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_range_less_than_patch(): void
    {
        $this->artisan('semver:check "<7.5.0" 7.4.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_range_less_than_equal(): void
    {
        $this->artisan('semver:check "<7.0.0" 7.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_range_less_than_or_equal_to_major(): void
    {
        $this->artisan('semver:check "<=7.0.0" 6.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_range_less_than_or_equal_to_patch(): void
    {
        $this->artisan('semver:check "<=7.5.0" 6.4.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_range_less_than_or_equal_to_equal(): void
    {
        $this->artisan('semver:check "<=7.0.0" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_range_less_than_or_equal_to_less(): void
    {
        $this->artisan('semver:check "<=7.0.0" 7.0.1')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_basic_and(): void
    {
        $this->artisan('semver:check "7.0.0, 7.0.0" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_basic_and(): void
    {
        $this->artisan('semver:check "7.0.0, 7.0.1" 7.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_basic_or(): void
    {
        $this->artisan('semver:check "7.0.0 || 7.0.1" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_basic_or(): void
    {
        $this->artisan('semver:check "7.0.0 || 7.0.1" 7.0.2')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_double_or(): void
    {
        $this->artisan('semver:check "7.0.0 || 7.0.1 || 7.0.2" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_and_with_or(): void
    {
        $this->artisan('semver:check "7.0.0 || 7.0.1, 7.0.2" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_range_constraint_without_patch(): void
    {
        $this->artisan('semver:check ">7.0" 7.0.1')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_range_constraint_without_minor(): void
    {
        $this->artisan('semver:check ">7.0" 7.0.1')
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
