<?php

namespace App\Models;

readonly abstract class PartialConstraint
{
    // Modifications

    abstract public function minimum(): SingleConstraint;

    abstract public function maximum(): SingleConstraint;
}

