<?php

namespace Puzzle\Core;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class YamlLoader
{
    public function __construct(protected array $folders)
    {
    }

    public function findManifests(string $name = '*.info.yaml', int $depth = 1): array
    {
        $finder = new Finder();
        $finder->files()->in($this->folders)->name($name)->depth('== ' . $depth);

        $manifests = [];
        foreach ($finder as $file) {
            $content = Yaml::parseFile($file->getRealPath());
            $manifests[] = new Manifest($file, $content);
        }

        return $manifests;
    }
}
