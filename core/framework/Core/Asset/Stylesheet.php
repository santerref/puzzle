<?php

namespace Puzzle\Core\Asset;

class Stylesheet
{
    public function __construct(
        protected string $href,
        protected array $attributes = [],
        protected ?string $namespace = null
    ) {
    }

    public function getRel(): string
    {
        return 'stylesheet';
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function hasNamespace(): bool
    {
        return !empty($this->namespace);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
