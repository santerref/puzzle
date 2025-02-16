<?php

namespace Puzzle\Template\Asset;

use Puzzle\Puzzle;
use Puzzle\Template\Asset\VersionStrategy\ViteManifestVersionStrategy;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class ViteAssetPackage extends PathPackage
{
    protected bool $isDevMode;

    public function __construct(string $basePath)
    {
        $this->isDevMode = Puzzle::config()->get('puzzle.dev_mode', false);
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

        return '/static/' . preg_replace(
            '/^' . preg_quote($this->getBasePath(), '/') . '/i',
            '',
            parent::getUrl(ltrim($this->getBasePath(), '/') . $path)
        );
    }
}
