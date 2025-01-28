<?php

namespace Puzzle\Http;

class TwigTemplateResponse
{
    public function __construct(
        protected string $template,
        protected array $args = []
    ) {
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}
