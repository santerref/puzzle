<?php

namespace Puzzle\Core\Component\FieldType;

use Illuminate\Support\Str;

abstract class FieldTypeBase implements FieldTypeInterface
{
    public function id(): string
    {
        $reflection = new \ReflectionClass($this);
        return Str::snake($reflection->getShortName());
    }

    public function validateValue(mixed $value): void
    {
    }

    public function validateSettings(array $settings): void
    {
    }
}
