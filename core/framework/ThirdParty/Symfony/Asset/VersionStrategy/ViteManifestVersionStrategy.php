<?php

namespace Puzzle\ThirdParty\Symfony\Asset\VersionStrategy;

use Symfony\Component\Asset\Exception\RuntimeException;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class ViteManifestVersionStrategy implements VersionStrategyInterface
{
    private array $manifestData;

    public function __construct(private string $manifestPath)
    {
    }

    public function getVersion(string $path): string
    {
        return $this->applyVersion($path);
    }

    public function applyVersion(string $path): string
    {
        return $this->getManifestPath($path) ?: $path;
    }

    private function getManifestPath(string $path): ?string
    {
        if (!isset($this->manifestData)) {
            if (!is_file($this->manifestPath)) {
                throw new RuntimeException(\sprintf('Asset manifest file "%s" does not exist. Did you forget to build the assets with npm or yarn?', $this->manifestPath));
            }

            try {
                $this->manifestData = json_decode(file_get_contents($this->manifestPath), true, flags: \JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new RuntimeException(\sprintf('Error parsing JSON from asset manifest file "%s": ', $this->manifestPath) . $e->getMessage(), previous: $e);
            }
        }

        if (isset($this->manifestData[$path])) {
            return $this->manifestData[$path]['file'];
        }

        return null;
    }
}
