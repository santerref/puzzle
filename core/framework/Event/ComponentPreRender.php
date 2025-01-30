<?php

namespace Puzzle\Event;

use Puzzle\Component\Component;
use Symfony\Contracts\EventDispatcher\Event;

class ComponentPreRender extends Event
{
    public const NAME = 'component.pre_render';

    public function __construct(
        private readonly Component $component,
        private array $formValues
    ) {
    }

    public function getComponent(): Component
    {
        return $this->component;
    }

    public function getFormValues(): array
    {
        return $this->formValues;
    }

    public function setValues($formValues): void
    {
        $this->formValues = $formValues;
    }
}
