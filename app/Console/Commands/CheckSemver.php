<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as ConsoleCommand;

class CheckSemver extends Command
{
    protected $signature = 'semver:check {constraint} {version}';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        return ConsoleCommand::FAILURE;
    }
}
