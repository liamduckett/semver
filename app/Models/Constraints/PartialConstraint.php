<?php

namespace App\Models\Constraints;

readonly abstract class PartialConstraint
{
    // Modifications

    abstract public function minimum(): SingleConstraint;

    abstract public function maximum(): SingleConstraint;
}

