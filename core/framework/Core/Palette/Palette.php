<?php

namespace Puzzle\Core\Palette;

use Puzzle\Core\Registrable;
use Puzzle\Core\Validator\DefinitionValidatorInterface;

class Palette extends Registrable
{
    protected function getValidator(): DefinitionValidatorInterface
    {
        return new PaletteValidator();
    }
}
