<?php

namespace App\Enums;

use App\Models\Constraints\GroupConstraint;
use App\Models\Version;

enum Operator: string
{
    case And = ',';
    case Or = '||';

    public function allows(Version $version, GroupConstraint $constraint): bool
    {
        return match($this) {
            self::And => $constraint->first->allows($version) && $constraint->second->allows($version),
            self::Or => $constraint->first->allows($version) || $constraint->second->allows($version),
        };
    }
}
