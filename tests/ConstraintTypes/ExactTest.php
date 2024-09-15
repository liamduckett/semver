<?php


namespace ConstraintTypes;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class ExactTest extends TestCase
{
    #[Test]
    public function returns_failure_for_exact_check(): void
    {
        $this->artisan('semver:check 8.0.0 8.0.1')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }
}
