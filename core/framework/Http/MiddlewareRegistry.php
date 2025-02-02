<?php

namespace Puzzle\Http;

use Puzzle\Http\Middleware\MiddlewareInterface;

class MiddlewareRegistry
{
    private array $middlewares = [];

    public function add(string $name, MiddlewareInterface $middleware): void
    {
        $this->middlewares[$name] = $middleware;
    }

    public function get($name): ?MiddlewareInterface
    {
        return $this->middlewares[$name] ?? null;
    }

    public function has($name): bool
    {
        return isset($this->middlewares[$name]);
    }

    public function all(): array
    {
        return $this->middlewares;
    }
}
