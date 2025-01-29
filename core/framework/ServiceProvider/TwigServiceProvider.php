<?php

namespace Puzzle\ServiceProvider;

use Puzzle\ThirdParty\Symfony\ViteAssetPackage;
use Puzzle\ThirdParty\Twig\PuzzleExtension;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigServiceProvider implements ServiceProviderInterface
{
    public static function register(ContainerBuilder $container): void
    {
        $pathPackage = new ViteAssetPackage('/core/assets');

        $loader = new FilesystemLoader([
            PUZZLE_ROOT . '/core/templates',
            PUZZLE_ROOT . '/core/components'
        ]);
        $loader->addPath(PUZZLE_ROOT . '/core/templates', 'core');
        $twig = new Environment($loader, [
            'cache' => false
        ]);
        $packages = new Packages($pathPackage, [
            'core' => $pathPackage
        ]);
        $twig->addExtension(new AssetExtension($packages));
        $twig->addExtension(new PuzzleExtension());
        $container->set('twig', $twig);
        $container->set('asset.packages', $packages);
    }
}
