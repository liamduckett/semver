<?php


namespace ConstraintTypes\Partials;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class WildcardRangeTest extends TestCase
{
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
}
