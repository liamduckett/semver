<?php


namespace ConstraintTypes\Partials;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class HyphenatedRangeTest extends TestCase
{
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
}
