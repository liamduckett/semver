<?php

namespace App\Console\Commands;

use App\Rules\IsSemVer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Command\Command as ConsoleCommand;

class CheckSemVer extends Command
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
            'constraint' => ['string', new IsSemVer],
            'version' => ['string', new IsSemVer],
        ]);

        if ($validator->fails()) {
            foreach($validator->errors()->all() as $error) {
                $this->error($error);
            }
        }

        return $validator->passes();
    }
}
