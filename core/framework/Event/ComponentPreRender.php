<?php

namespace Puzzle\Event;

use Puzzle\Component\Component;
use Symfony\Contracts\EventDispatcher\Event;

class ComponentPreRender extends Event
{
    public const NAME = 'component.pre_render';

    public function __construct(private readonly Component $component)
    {
    }

    public function getComponent()
    {
        return $this->component;
    }
}
