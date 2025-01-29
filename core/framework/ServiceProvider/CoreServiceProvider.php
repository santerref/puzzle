<?php

namespace Puzzle\ServiceProvider;

use GuzzleHttp\Client;
use Illuminate\Database\Capsule\Manager as Capsule;
use Puzzle\ThirdParty\Symfony\ViteAssetPackage;
use Puzzle\ThirdParty\Twig\PuzzleExtension;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\Asset\Packages;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerTwig();
        $this->registerStorage();
        $this->registerHttpClient();
    }

    private function registerTwig(): void
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
        $this->container->set('twig', $twig);
        $this->container->set('asset.packages', $packages);
    }

    private function registerStorage(): void
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'db',
            'database'  => 'db',
            'username'  => 'db',
            'password'  => 'db',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    private function registerHttpClient(): void
    {
        $this->container->register('http_client', Client::class)
            ->setPublic(true);
        $this->container->setAlias(Client::class, 'http_client');
    }
}
