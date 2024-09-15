<?php


namespace ConstraintTypes;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class NotTest extends TestCase
{
    #[Test]
    public function passes_positive_not_check(): void
    {
        $this->artisan('semver:check "!=8.0.0" 8.0.1')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_not_check(): void
    {
        $this->artisan('semver:check "!=8.0.0" 8.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }
}
