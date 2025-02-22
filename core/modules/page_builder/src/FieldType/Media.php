<?php

namespace Puzzle\page_builder\FieldType;

use Puzzle\Core\Component\FieldType\FieldTypeBase;
use Puzzle\Enums\FieldValueType;

class Media extends FieldTypeBase
{
    public function valueType(): FieldValueType
    {
        return FieldValueType::Json;
    }
}
