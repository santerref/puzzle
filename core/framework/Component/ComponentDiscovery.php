<?php

namespace Puzzle\Component;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ComponentDiscovery
{
    protected string $folder;

    protected array $components = [];

    public function __construct($folder)
    {
        $this->folder = $folder;
    }

    public function discover(): void
    {
        $finder = new Finder();
        $finder->files()->in($this->folder)->name('*.info.yaml')->depth('== 1');
        foreach ($finder as $file) {
            $this->components[$file->getRelativePath()] = Component::createFromInfo(
                $file->getRelativePath(),
                $file->getPath(),
                Yaml::parseFile($file->getRealPath())
            );
        }
    }

    public function getComponents(): array
    {
        return $this->components;
    }

    public function get(string $id): Component
    {
        return $this->components[$id];
    }
}
