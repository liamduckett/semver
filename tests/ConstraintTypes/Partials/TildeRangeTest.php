<?php


namespace ConstraintTypes\Partials;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class TildeRangeTest extends TestCase
{
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
}
