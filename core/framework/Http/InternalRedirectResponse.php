<?php

namespace Puzzle\Http;

class InternalRedirectResponse
{
    public function __construct(
        protected string $route,
        protected array $parameters = [],
        protected int $status = 302
    ) {
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
