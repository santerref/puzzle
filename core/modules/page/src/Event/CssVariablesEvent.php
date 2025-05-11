<?php

namespace Puzzle\page\Event;

use Symfony\Contracts\EventDispatcher\Event;

class CssVariablesEvent extends Event
{
    public const string NAME = 'page.css_variables';

    protected array $variables = [];

    public function addVariables(array $variables): void
    {
        $this->variables = array_merge($this->variables, $variables);
    }

    public function setVariable(string $key, string $value): void
    {
        $this->variables[$key] = $value;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }
}
