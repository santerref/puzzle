<?php

namespace Puzzle\page_builder;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Puzzle\Core\Component\ComponentType;
use Puzzle\page_builder\Entity\Component;

class ComponentFactory
{
    public function create(ComponentType $componentType): Component
    {
        $component = new Component([
            'id' => Str::uuid(),
            'component_type' => $componentType->getType(),
        ]);

        $componentFields = new Collection();
        foreach ($componentType->getFields() as $field) {
            $componentFields->push($component->componentFields()->make([
                'id' => Str::uuid(),
                'field_name' => $field->id(),
                'field_type' => $field->getFieldType()->valueType()->value,
                'component_id' => $component->id,
                $field->getFieldType()->valueType()->value . '_value' => $field->getDefaultValue()
            ]));
        }
        $component->setRelation('componentFields', $componentFields);

        return $component;
    }
}
