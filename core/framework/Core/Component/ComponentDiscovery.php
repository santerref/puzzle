<?php

namespace Puzzle\Core\Component;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ComponentDiscovery
{
    protected array $components = [];

    public function __construct(protected array $folder)
    {
    }

    public function discover(): void
    {
        $finder = new Finder();
        $finder->files()->in($this->folder)->name('*.info.yaml')->depth('== 1');
        foreach ($finder as $file) {
            $info = Yaml::parseFile($file->getRealPath());
            $version = $info['version'];
            $this->components[$file->getRelativePath()] = ComponentType::createFromInfo(
                $file->getRelativePath(),
                $file->getPath(),
                $info,
                $version
            );
        }
    }

    public function getComponents(): array
    {
        return $this->components;
    }

    public function get(string $id): ComponentType
    {
        return $this->components[$id];
    }
}
