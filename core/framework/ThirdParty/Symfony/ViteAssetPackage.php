<?php

namespace Puzzle\ThirdParty\Symfony;

use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

class ViteAssetPackage extends PathPackage
{
    //@TODO: Move this in configuration + .env.
    protected bool $isDevMode = true;

    public function __construct(string $basePath)
    {
        $manifestPath = PUZZLE_ROOT . '/public/static/manifest.json';
        $versionStrategy = $this->isDevMode
            ? new EmptyVersionStrategy()
            : new JsonManifestVersionStrategy($manifestPath);

        parent::__construct($basePath, $versionStrategy);
    }

    public function getUrl(string $path): string
    {
        if ($this->isDevMode) {
            return sprintf(
                'https://vite.cms-custom.ddev.site' . str_replace('/assets/', '/', $this->getBasePath()) . '%s',
                ltrim($path, '/')
            );
        }

        return parent::getUrl($path);
    }
}
