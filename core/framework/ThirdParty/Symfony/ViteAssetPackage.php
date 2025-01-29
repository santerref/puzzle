<?php

namespace Puzzle\ThirdParty\Symfony;

use Puzzle\ThirdParty\Symfony\Asset\VersionStrategy\ViteManifestVersionStrategy;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class ViteAssetPackage extends PathPackage
{
    //@TODO: Move this in configuration + .env.
    protected bool $isDevMode = true;

    public function __construct(string $basePath)
    {
        $manifestPath = PUZZLE_ROOT . '/public/static/manifest.json';
        $versionStrategy = $this->isDevMode
            ? new EmptyVersionStrategy()
            : new ViteManifestVersionStrategy($manifestPath);

        parent::__construct($basePath, $versionStrategy);
    }

    public function getUrl(string $path): string
    {
        if ($this->isDevMode) {
            return sprintf(
                'https://vite.puzzle.ddev.site' . $this->getBasePath() . '%s',
                ltrim($path, '/')
            );
        }

        if ($this->isDevMode) {
            return parent::getUrl($path);
        } else {
            return 'static/' . preg_replace(
                '/^' . preg_quote($this->getBasePath(), '/') . '/i',
                '',
                parent::getUrl(ltrim($this->getBasePath(), '/') . $path)
            );
        }
    }
}
