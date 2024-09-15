<?php


namespace ConstraintTypes;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class GreaterThanOrEqualToTest extends TestCase
{
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
    public function allows_range_greater_than_or_equal_to_major(): void
    {
        $this->artisan('semver:check ">=7.0.0" 8.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }
}
