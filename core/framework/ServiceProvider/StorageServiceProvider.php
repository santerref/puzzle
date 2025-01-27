<?php

namespace Puzzle\ServiceProvider;

use MongoDB\Client;
use Puzzle\Storage\MongoDbStorage;
use Puzzle\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StorageServiceProvider implements ServiceProviderInterface
{
    public static function register(ContainerBuilder $container): void
    {
        $mongoClient = new Client('mongodb://db:db@mongo:27017');
        $storage = new MongoDbStorage($mongoClient, 'puzzle', 'models');
        $container->set('storage', $storage);
        $container->setAlias('storage', StorageInterface::class);
    }
}
