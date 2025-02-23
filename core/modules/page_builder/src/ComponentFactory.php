<?php

namespace Puzzle\page_builder;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Puzzle\Core\Component\ComponentType;
use Puzzle\page_builder\Entity\Component;
use Puzzle\page_builder\Entity\ComponentField;

class ComponentFactory
{
    public function create(string $uuid, ComponentType $componentType, array $componentFields = []): Component
    {
        $component = new Component([
            'id' => $uuid,
            'component_type' => $componentType->getType(),
        ]);

        $fieldsCollection = new Collection();
        if (!empty($componentFields)) {
            foreach ($componentFields as $field) {
                $fieldsCollection->push($component->componentFields()->make($field));
            }
        } else {
            foreach ($componentType->getFields() as $field) {
                $componentField = new ComponentField([
                    'id' => Str::uuid(),
                    'component_id' => $component->id,
                    'field_name' => $field->id(),
                    'field_type' => $field->getFieldType()->id(),
                    'value_type' => $field->getFieldType()->valueType()->value,
                    'int_value' => null,
                    'varchar_value' => null,
                    'text_value' => null,
                    'json_value' => null,
                    'bool_value' => null,
                    'blob_value' => null,
                    'weight' => 0,
                ]);
                $componentField->setAttribute(
                    $field->getFieldType()->valueType()->value . '_value',
                    $field->getDefaultValue()
                );
                $fieldsCollection->push($componentField);
            }
        }
        $component->setRelation('componentFields', $fieldsCollection);

        return $component;
    }
}
