<?php

namespace App\Models\Constraints\Partial;

use App\Models\Constraints\SingleConstraint;

readonly abstract class PartialConstraint
{
    // Modifications

    abstract public function minimum(): SingleConstraint;

    abstract public function maximum(): SingleConstraint;
}

