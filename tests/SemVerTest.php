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
    public function allows_basic_hyphenated_range_constraint(): void
    {
        $this->artisan('semver:check "7.0.0 - 8.0.0" 7.5.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_basic_hyphenated_range_lower_bound(): void
    {
        $this->artisan('semver:check "7.0.0 - 8.0.0" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_basic_hyphenated_range_upper_bound(): void
    {
        $this->artisan('semver:check "7.0.0 - 8.0.0" 8.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_basic_hyphenated_range(): void
    {
        $this->artisan('semver:check "7.0.0 - 8.0.0" 6.9.9')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_basic_hyphenated_range_above_upper_bound(): void
    {
        $this->artisan('semver:check "7.0.0 - 8.0.0" 8.0.1')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_hyphenated_range_with_or(): void
    {
        $this->artisan('semver:check "7.0.0 - 8.0.0 || 9.0.1" 7.5.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_hyphenated_range_with_missing_patch(): void
    {
        $this->artisan('semver:check "1.0 - 2.0" 2.0.5')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_hyphenated_range_with_missing_minor(): void
    {
        $this->artisan('semver:check "1 - 2" 2.9.9')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_hyphenated_range_with_missing_minor(): void
    {
        $this->artisan('semver:check "1 - 2" 3.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_wildcard_range_on_patch(): void
    {
        $this->artisan('semver:check "1.0.*" 1.0.5')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_wildcard_range_on_patch_lower_bound(): void
    {
        $this->artisan('semver:check "1.0.*" 1.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_wildcard_range_on_patch(): void
    {
        $this->artisan('semver:check "1.0.*" 1.1.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_wildcard_range_on_minor(): void
    {
        $this->artisan('semver:check "1.*" 1.0.5')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_wildcard_range_on_major(): void
    {
        $this->artisan('semver:check "*" 0.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_tilde_range_on_patch(): void
    {
        $this->artisan('semver:check "~1.2.3" 1.2.5')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_tilde_range_on_patch_lower_bound(): void
    {
        $this->artisan('semver:check "~1.2.3" 1.2.3')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_tilde_range_on_patch_upper_bound(): void
    {
        $this->artisan('semver:check "~1.2.3" 1.3.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_tilde_range_on_minor(): void
    {
        $this->artisan('semver:check "~1.2" 1.5.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_tilde_range_on_minor_lower_bound(): void
    {
        $this->artisan('semver:check "~1.2" 1.2.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_tilde_range_on_minor_upper_bound(): void
    {
        $this->artisan('semver:check "~1.2" 2.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_tilde_range_on_major(): void
    {
        $this->artisan('semver:check "~1" 1.5.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_tilde_range_on_major_lower_bound(): void
    {
        $this->artisan('semver:check "~1" 1.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_tilde_range_on_major_upper_bound(): void
    {
        $this->artisan('semver:check "~1" 2.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_caret_range_on_patch(): void
    {
        $this->artisan('semver:check "^1.2.3" 1.2.5')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_caret_range_on_patch_lower_bound(): void
    {
        $this->artisan('semver:check "^1.2.3" 1.2.3')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_caret_range_on_patch_upper_bound(): void
    {
        $this->artisan('semver:check "^1.2.3" 2.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_caret_range_on_major(): void
    {
        $this->artisan('semver:check "^1" 1.2.5')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function allows_caret_range_on_major_lower_bound(): void
    {
        $this->artisan('semver:check "^1" 1.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function rejects_caret_range_on_major_upper_bound(): void
    {
        $this->artisan('semver:check "^1" 2.0.0')
            ->expectsOutput('Fail')
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
