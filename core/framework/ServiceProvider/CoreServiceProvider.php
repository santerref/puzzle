<?php

namespace Puzzle\ServiceProvider;

use GuzzleHttp\Client;
use Illuminate\Database\Capsule\Manager as Capsule;
use Puzzle\Core\Config;
use Puzzle\Core\Logger\LoggerFactory;
use Puzzle\Puzzle;
use Puzzle\Template\Twig\PuzzleExtension;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Puzzle::setContainer($this->container);
        $this->registerSerializer();
        $this->registerLogger();
        $this->registerConfig();
        $this->registerTwig();
        $this->registerStorage();
        $this->registerHttpClient();
    }

    private function registerSerializer(): void
    {
        $this->container->register('serializer.json_encoder', JsonEncoder::class);

        $this->container->register('serializer.object_normalizer', ObjectNormalizer::class);

        $this->container->register('serializer', Serializer::class)
            ->addArgument([
                new Reference('serializer.object_normalizer')
            ])
            ->addArgument([
                'json' => new Reference('serializer.json_encoder'),
            ])
            ->setPublic(true);
        $this->container->setAlias(Serializer::class, 'serializer');
    }

    private function registerLogger(): void
    {
        $this->container->register('logger_factory', LoggerFactory::class)
            ->addArgument($this->container)
            ->setPublic(true);
        $this->container->setAlias(LoggerFactory::class, 'logger_factory');
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
                    [$env, $cast] = array_pad(explode(':', $value->getValue(), 2), 2, 'string');
                    $envValue = getenv($env) ?: $_ENV[$env] ?? null;
                    $envValue = match ($cast) {
                        'bool' => filter_var($envValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                        'int' => (int)$envValue,
                        'float' => (float)$envValue,
                        'string' => (string)$envValue,
                        'array' => is_string($envValue) ? explode(',', $envValue) : (array)$envValue,
                        'json' => json_decode($envValue, true),
                        default => $value,
                    };
                    $value = $envValue;
                }
            });
            $mergedConfig[$configName] = $parsedConfig;
        }

        $configDefinition = new Definition(Config::class);
        $this->container->setDefinition('config', $configDefinition)
            ->addArgument($mergedConfig);
        $this->container->setAlias(Config::class, 'config');
    }

    private function registerTwig(): void
    {
        $loader = new FilesystemLoader([
            PUZZLE_ROOT . '/core/components'
        ]);
        $twig = new Environment($loader, [
            'cache' => Puzzle::config()->get('twig.cache'),
            'debug' => Puzzle::config()->get('puzzle.dev_mode', false)
        ]);
        $packages = new Packages();
        $twig->addGlobal('dev_mode', Puzzle::config()->get('puzzle.dev_mode', false));
        $twig->addExtension(new AssetExtension($packages));
        $twig->addExtension(new PuzzleExtension($this->container));
        $this->container->set('twig', $twig);
        $this->container->set('asset.packages', $packages);
    }

    private function registerStorage(): void
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver' => Puzzle::config()->get('database.driver', 'mysql'),
            'host' => Puzzle::config()->get('database.host'),
            'database' => Puzzle::config()->get('database.database'),
            'username' => Puzzle::config()->get('database.username'),
            'password' => Puzzle::config()->get('database.password'),
            'charset' => Puzzle::config()->get('database.charset', 'utf8'),
            'collation' => Puzzle::config()->get('database.collation', 'utf8_unicode_ci'),
            'prefix' => Puzzle::config()->get('database.prefix', ''),
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
