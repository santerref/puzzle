<?php

namespace Puzzle\ServiceProvider;

use Puzzle\ThirdParty\Symfony\ViteAssetPackage;
use Puzzle\ThirdParty\Twig\PuzzleExtension;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigServiceProvider implements ServiceProvider
{
    public static function register(ContainerBuilder $container): void
    {
        $manifestPath = PUZZLE_ROOT . '/public/assets/manifest.json';
        $pathPackage = new ViteAssetPackage($manifestPath);

        $loader = new FilesystemLoader([
            PUZZLE_ROOT . '/core/resources/templates',
            PUZZLE_ROOT . '/public/components'
        ]);
        $twig = new Environment($loader, [
            'cache' => false
        ]);
        $packages = new Packages($pathPackage);
        $twig->addExtension(new AssetExtension($packages));
        $twig->addExtension(new PuzzleExtension());
        $container->set('twig', $twig);
    }
}
