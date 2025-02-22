<?php

namespace Puzzle\Core\Component;

use Puzzle\Event\ComponentPreRender;
use Puzzle\page_builder\Entity\Component;
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
        $r = array_merge(
            ['component' => $component->toTemplateArgs()],
            [
                'css' => $componentType->getSetting('css', []),
                'context' => $context
            ]
        );
        return $this->twig->render(
            $componentType->getTemplate(),
            array_merge(
                ['component' => $component->toTemplateArgs()],
                [
                    'css' => $componentType->getSetting('css', []),
                    'context' => $context
                ]
            )
        );
    }
}
