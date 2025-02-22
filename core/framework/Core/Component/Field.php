<?php

namespace Puzzle\Core\Component;

use Puzzle\Core\Component\FieldType\FieldTypeInterface;

class Field implements \JsonSerializable
{
    public function __construct(
        protected string $id,
        protected FieldTypeInterface $fieldType,
        protected string $label,
        protected mixed $defaultValue,
        protected array $settings = []
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function getFieldType(): FieldTypeInterface
    {
        return $this->fieldType;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->fieldType->id(),
            'label' => $this->label,
            'default_value' => $this->defaultValue,
            'settings' => $this->settings,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
