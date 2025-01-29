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

    public function render(Component $component, $defaultValue = false): string
    {
        $this->eventDispatcher->dispatch(new ComponentPreRender($component), ComponentPreRender::NAME);
        return $this->twig->render(
            $component->getTemplate(),
            $component->getArgs(!$defaultValue)
        );
    }
}
