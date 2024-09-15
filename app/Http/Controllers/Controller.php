<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckRequest;
use App\Models\Constraint;
use App\Models\Version;

final readonly class Controller
{
    public function __invoke(CheckRequest $request)
    {
        $validated = $request->validated();

        $constraint = Constraint::create($validated['constraint']);
        $version = Version::fromString($validated['version']);

        $pass = $constraint->allows($version);

        return view('welcome', [
            'pass' => $pass,
        ]);
    }
}
