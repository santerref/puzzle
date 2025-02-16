<?php

namespace Puzzle\Core\Component;

use Puzzle\page_builder\Entity\Component;
use Puzzle\Event\ComponentPreRender;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Twig\Environment;

class Renderer
{
    public function __construct(
        protected Environment $twig,
        protected EventDispatcher $eventDispatcher
    ) {
    }

    public function render(ComponentType $componentType, Component $component, array $context = []): string
    {
        $event = new ComponentPreRender($componentType, $component);
        $this->eventDispatcher->dispatch($event, ComponentPreRender::NAME);
        $formValues = $component->getAttribute('form_values');
        return $this->twig->render(
            $componentType->getTemplate(),
            array_merge(
                $formValues,
                [
                    'css' => $componentType->getSetting('css', []),
                    'context' => $context
                ]
            )
        );
    }
}
