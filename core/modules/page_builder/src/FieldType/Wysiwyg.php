<?php

namespace Puzzle\page_builder\FieldType;

use Puzzle\Core\Component\FieldType\FieldTypeBase;
use Puzzle\Enums\FieldValueType;

class Wysiwyg extends FieldTypeBase
{
    public function valueType(): FieldValueType
    {
        return FieldValueType::Varchar;
    }
}
