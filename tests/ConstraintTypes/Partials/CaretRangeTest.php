<?php


namespace ConstraintTypes\Partials;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class CaretRangeTest extends TestCase
{
    #[Test]
    public function allows_caret_range_on_patch(): void
    {
        $this->artisan('semver:check "^1.2.3" 1.4.5')
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
}
