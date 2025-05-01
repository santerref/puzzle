<?php

namespace Puzzle\Core\Palette;

use Puzzle\Core\Validator\BaseValidator;

class PaletteValidator extends BaseValidator
{
    public function getRequiredFields(): array
    {
        return [
                'colors',
            ] + parent::getRequiredFields();
    }
}
