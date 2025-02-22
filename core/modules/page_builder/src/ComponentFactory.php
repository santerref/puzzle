<?php

namespace Puzzle\page_builder;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Puzzle\Core\Component\ComponentType;
use Puzzle\page_builder\Entity\Component;

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
                $fieldsCollection->push($component->componentFields()->make([
                    'id' => Str::uuid(),
                    'field_name' => $field->id(),
                    'field_type' => $field->getFieldType()->id(),
                    'value_type' => $field->getFieldType()->valueType()->value,
                    'component_id' => $component->id,
                    $field->getFieldType()->valueType()->value . '_value' => $field->getDefaultValue()
                ]));
            }
        }
        $component->setRelation('componentFields', $fieldsCollection);

        return $component;
    }
}
