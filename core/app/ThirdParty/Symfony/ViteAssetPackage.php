<?php

namespace Puzzle\ThirdParty\Symfony;

use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

class ViteAssetPackage extends PathPackage
{
    protected bool $isDevMode = true;

    public function __construct(string $manifestPath = null, string $basePath = '/assets')
    {
        $versionStrategy = $this->isDevMode
            ? new EmptyVersionStrategy()
            : new JsonManifestVersionStrategy($manifestPath);

        parent::__construct($basePath, $versionStrategy);
    }

    public function getUrl(string $path): string
    {
        if ($this->isDevMode) {
            return sprintf('https://vite.cms-custom.ddev.site/%s', ltrim($path, '/'));
        }

        return parent::getUrl($path);
    }
}
