<?php

namespace Puzzle\Core;

class Registry
{
    public function __construct(
        protected array $items
    ) {
    }

    public function get(string $name): mixed
    {
        return $this->items[$name] ?? null;
    }

    public function all(): array
    {
        return $this->items;
    }
}
