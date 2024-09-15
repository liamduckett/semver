<?php


namespace Operations;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class OrTest extends TestCase
{
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
}
