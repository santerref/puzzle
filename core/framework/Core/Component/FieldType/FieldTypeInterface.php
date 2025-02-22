<?php

namespace Puzzle\Core\Component\FieldType;

use Puzzle\Enums\FieldValueType;

interface FieldTypeInterface
{
    public function id(): string;

    public function valueType(): FieldValueType;

    public function validateValue(mixed $value): void;

    public function validateSettings(array $settings): void;
}
