<?php

namespace Puzzle\Module;

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
}
