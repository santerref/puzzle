<?php

namespace Puzzle\Core\Module;

use Illuminate\Support\Arr;
use Puzzle\Core\Registrable;

class Module extends Registrable
{
    public function getDependencies(): array
    {
        return Arr::get($this->definition, 'dependencies', []);
    }
}
