<?php


namespace ConstraintTypes\Partials;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

class CaretRangeTest extends TestCase
{
    #[Test]
    public function passes_positive_caret_on_patch(): void
    {
        $this->artisan('semver:check "^1.2.3" 1.2.4')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_on_minor(): void
    {
        $this->artisan('semver:check "^1.2.3" 1.3.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_lower_bound(): void
    {
        $this->artisan('semver:check "^1.2.3" 1.2.3')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_caret_on_major(): void
    {
        $this->artisan('semver:check "^1.2.3" 2.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_missing_patch_on_patch(): void
    {
        $this->artisan('semver:check "^1.2" 1.2.1')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_missing_patch_on_minor(): void
    {
        $this->artisan('semver:check "^1.2" 1.3.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_missing_patch_lower_bound(): void
    {
        $this->artisan('semver:check "^1.2" 1.2.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_caret_missing_patch_on_major(): void
    {
        $this->artisan('semver:check "^1.2" 2.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_missing_minor_on_patch(): void
    {
        $this->artisan('semver:check "^1" 1.0.1')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_missing_minor_on_minor(): void
    {
        $this->artisan('semver:check "^1" 1.1.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_missing_minor_lower_bound(): void
    {
        $this->artisan('semver:check "^1" 1.0.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_caret_missing_minor_on_major(): void
    {
        $this->artisan('semver:check "^1" 2.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_pre_release_on_patch(): void
    {
        $this->artisan('semver:check "^0.2.3" 0.2.4')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_pre_release_lower_bound(): void
    {
        $this->artisan('semver:check "^0.2.3" 0.2.3')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_caret_pre_release_on_minor(): void
    {
        $this->artisan('semver:check "^0.2.3" 0.3.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_caret_pre_release_on_major(): void
    {
        $this->artisan('semver:check "^0.2.3" 1.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_pre_release_missing_patch_on_patch(): void
    {
        $this->artisan('semver:check "^0.2" 0.2.1')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function passes_positive_caret_pre_release_missing_patch_lower_bound(): void
    {
        $this->artisan('semver:check "^0.2" 0.2.0')
            ->expectsOutput('Pass')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_caret_pre_release_missing_patch_on_minor(): void
    {
        $this->artisan('semver:check "^0.2" 0.3.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }

    #[Test]
    public function fails_negative_caret_pre_release_missing_patch_on_major(): void
    {
        $this->artisan('semver:check "^0.2" 1.0.0')
            ->expectsOutput('Fail')
            ->assertExitCode(Command::SUCCESS);
    }
}
