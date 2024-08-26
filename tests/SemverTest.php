<?php

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;
use Illuminate\Foundation\Testing\TestCase;

class SemverTest extends TestCase
{
    #[Test]
    public function can_run_semver_check_command(): void
    {
        $this->artisan('semver:check 8.0.0 7.0.0')
            ->assertExitCode(Command::FAILURE);
    }
}
