<?php

namespace Puzzle\Core\Asset;

class Script
{
    public function __construct(
        protected string $src,
        protected array $attributes = [],
        protected ?string $namespace = null
    ) {
    }

    public function getSrc(): string
    {
        return $this->src;
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
        return array_merge(['type' => 'module'], $this->attributes);
    }
}
