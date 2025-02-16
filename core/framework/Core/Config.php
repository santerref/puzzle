<?php

namespace Puzzle\Core;

use Illuminate\Support\Arr;

class Config
{
    public function __construct(private array $config = [])
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->config, $key, $default);
    }
}
