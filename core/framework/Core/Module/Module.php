<?php

namespace Puzzle\Core\Module;

use Illuminate\Support\Arr;

class Module
{
    public function __construct(
        //@TODO: Better $name or $id for the relativePath?
        protected string $name,
        protected string $path,
        protected array $definition
    ) {
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDefinition(): array
    {
        return $this->definition;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getNamespace(): string
    {
        return 'Puzzle\\' . $this->getName();
    }

    public function getDependencies(): array
    {
        return Arr::get($this->definition, 'dependencies', []);
    }
}
