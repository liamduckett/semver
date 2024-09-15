<?php

namespace App\Console\Commands;

use App\Models\Constraint;
use App\Models\Version;
use App\Rules\IsConstraint;
use App\Rules\IsVersion;
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

        $constraint = Constraint::create($constraintInput);
        $version = Version::fromString($versionInput);

        $output = $constraint->allows($version) ? 'Pass' : 'Fail';

        $this->line($output);

        return ConsoleCommand::SUCCESS;
    }

    protected function validateArguments(): bool
    {
        $validator = Validator::make($this->arguments(), [
            'constraint' => ['string', new IsConstraint],
            'version' => ['string', new IsVersion],
        ]);

        if ($validator->fails()) {
            foreach($validator->errors()->all() as $error) {
                $this->error($error);
            }
        }

        return $validator->passes();
    }
}
