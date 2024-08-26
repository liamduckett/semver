<?php

namespace App\Console\Commands;

use App\Rules\IsSemver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Command\Command as ConsoleCommand;

class CheckSemver extends Command
{
    protected $signature = 'semver:check {constraint} {version}';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if(! $this->validateArguments()) {
            return ConsoleCommand::FAILURE;
        }

        $constraintInput = $this->argument('constraint');
        $versionInput = $this->argument('version');

        return ConsoleCommand::SUCCESS;
    }

    protected function validateArguments(): bool
    {
        $validator = Validator::make($this->arguments(), [
            'constraint' => ['string', new IsSemver],
            'version' => ['string', new IsSemver],
        ]);

        if ($validator->fails()) {
            foreach($validator->errors()->all() as $error) {
                $this->error($error);
            }
        }

        return $validator->passes();
    }
}
