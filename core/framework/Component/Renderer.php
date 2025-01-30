<?php

namespace Puzzle\Component;

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

    public function render(Component $component, array $formValues): string
    {
        $event = new ComponentPreRender($component, $formValues);
        $this->eventDispatcher->dispatch($event, ComponentPreRender::NAME);
        $formValues = $event->getFormValues();
        return $this->twig->render(
            $component->getTemplate(),
            //@TODO: Refactor this, because css can be a form input.
            $formValues + ['css' => $component->getInfo()['settings']['css'] ?? []]
        );
    }
}
