<?php

namespace App\Http\Controllers;

use App\Models\Constraint;
use App\Models\Version;
use App\Rules\IsConstraint;
use App\Rules\IsVersion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final readonly class Homepage
{
    public function __invoke(Request $request): View
    {
        $request->mergeIfMissing([
            'constraint' => '^1.2.3',
            'version' => '1.4.5',
        ]);

        $request->validate([
            'constraint' => ['required', 'string', new IsConstraint],
            'version' => ['required', 'string', new IsVersion],
        ]);

        $constraint = Constraint::create($request->constraint);
        $version = Version::fromString($request->version);

        $pass = $constraint->allows($version);

        return view('welcome', [
            'pass' => $pass,
            'constraint' => $request->constraint,
            'version' => $request->version,
        ]);
    }
}
