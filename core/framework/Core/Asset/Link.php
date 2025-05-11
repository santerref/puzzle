<?php

namespace Puzzle\Core\Asset;

class Link
{
    public function __construct(
        protected string $rel,
        protected array $attributes = []
    ) {
    }

    public function getRel(): string
    {
        return $this->rel;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
