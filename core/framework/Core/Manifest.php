<?php

namespace Puzzle\Core;

class Manifest
{
    public function __construct(protected \SplFileInfo $file, protected array $definition)
    {
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    public function getDefinition(): array
    {
        return $this->definition;
    }
}
