<?php


namespace ConstraintTypes;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class LessThanOrEqualToTest extends TestCase
{
    #[Test]
    public function passes_positive_less_than_or_equal_to_major(): void
    {
        $this->artisan('semver:check "<=7.0.0" 6.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_less_than_or_equal_to_minor(): void
    {
        $this->artisan('semver:check "<=7.1.0" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_less_than_or_equal_to_patch(): void
    {
        $this->artisan('semver:check "<=7.0.1" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_less_than_or_equal_to_equal_to(): void
    {
        $this->artisan('semver:check "<=7.0.0" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_less_than_or_equal_to_lower_bound(): void
    {
        $this->artisan('semver:check "<=7.0.0" 7.0.1')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_less_than_or_equal_to_without_patch_major(): void
    {
        $this->artisan('semver:check "<=7.1" 7.0.1')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_less_than_or_equal_to_without_patch_equal_to(): void
    {
        $this->artisan('semver:check "<=7.0" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_less_than_or_equal_to_without_patch_lower_bound(): void
    {
        $this->artisan('semver:check "<=7.0" 7.0.1')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_less_than_or_equal_to_without_minor_major(): void
    {
        $this->artisan('semver:check "<=7" 6.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_less_than_or_equal_to_without_minor_equal_to(): void
    {
        $this->artisan('semver:check "<=7" 7.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_less_than_or_equal_to_without_minor_lower_bound(): void
    {
        $this->artisan('semver:check "<=7" 7.0.1')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }
}
