<?php


namespace ConstraintTypes;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class LessThanOrEqualToTest extends TestCase
{
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
}
