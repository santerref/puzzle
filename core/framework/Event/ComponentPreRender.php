<?php

namespace Puzzle\Event;

use Puzzle\Core\Component\ComponentType;
use Puzzle\page_builder\Entity\Component;
use Symfony\Contracts\EventDispatcher\Event;

class ComponentPreRender extends Event
{
    public const NAME = 'component.pre_render';

    public function __construct(
        private readonly ComponentType $componentType,
        private readonly Component $component
    ) {
    }

    public function getComponentType(): ComponentType
    {
        return $this->componentType;
    }

    public function getComponent(): Component
    {
        return $this->component;
    }
}
