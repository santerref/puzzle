<?php

namespace Puzzle\ServiceProvider;

use GuzzleHttp\Client;
use Illuminate\Database\Capsule\Manager as Capsule;
use Puzzle\Config;
use Puzzle\ThirdParty\Symfony\ViteAssetPackage;
use Puzzle\ThirdParty\Twig\PuzzleExtension;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
        $this->registerTwig();
        $this->registerStorage();
        $this->registerHttpClient();
    }

    private function registerConfig(): void
    {
        $configDirectory = PUZZLE_ROOT . '/config';
        $finder = new Finder();
        $finder->files()->in($configDirectory)->name('*.yaml')->depth('== 0');

        $mergedConfig = [];
        foreach ($finder as $file) {
            $configName = $file->getFilenameWithoutExtension();
            $parsedConfig = Yaml::parseFile($file->getRealPath(), Yaml::PARSE_CUSTOM_TAGS);
            array_walk_recursive($parsedConfig, function (&$value) {
                if ($value instanceof TaggedValue) {
                    $envValue = getenv($value->getValue()) ?: $_ENV[$value->getValue()] ?? null;
                    $value = $envValue;
                }
            });
            $mergedConfig[$configName] = $parsedConfig;
        }

        $this->container->setParameter('config', $mergedConfig);
        Config::setContainer($this->container);
    }

    private function registerTwig(): void
    {
        $loader = new FilesystemLoader([
            PUZZLE_ROOT . '/core/components'
        ]);
        $twig = new Environment($loader, [
            'cache' => Config::get('twig.cache')
        ]);
        $packages = new Packages();
        $twig->addExtension(new AssetExtension($packages));
        $twig->addExtension(new PuzzleExtension());
        $this->container->set('twig', $twig);
        $this->container->set('asset.packages', $packages);
    }

    private function registerStorage(): void
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => Config::get('database.driver', 'mysql'),
            'host'      => Config::get('database.host'),
            'database'  => Config::get('database.database'),
            'username'  => Config::get('database.username'),
            'password'  => Config::get('database.password'),
            'charset'   => Config::get('database.charset', 'utf8'),
            'collation' => Config::get('database.collation', 'utf8_unicode_ci'),
            'prefix'    => Config::get('database.prix', ''),
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
