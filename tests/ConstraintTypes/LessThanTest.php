<?php


namespace ConstraintTypes;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class LessThanTest extends TestCase
{
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
}
