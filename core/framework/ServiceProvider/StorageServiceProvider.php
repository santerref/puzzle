<?php

namespace Puzzle\ServiceProvider;

use Illuminate\Database\Capsule\Manager as Capsule;
use Puzzle\Storage\Database;
use Puzzle\Storage\MySqlStorage;
use Puzzle\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StorageServiceProvider implements ServiceProviderInterface
{
    public static function register(ContainerBuilder $container): void
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

        $container->set('storage', new MySqlStorage());
        $container->setAlias('storage', StorageInterface::class);
    }
}
