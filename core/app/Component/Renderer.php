<?php

namespace Puzzle\Component;

use Twig\Environment;

class Renderer
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(Component $component, $defaultValue = false): string
    {
        return $this->twig->render($component->getTemplate(), $component->getArgs(!$defaultValue));
    }
}
