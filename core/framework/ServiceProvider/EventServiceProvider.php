<?php

namespace Puzzle\ServiceProvider;

use Puzzle\EventSubscriber\BootstrapModule;
use Puzzle\EventSubscriber\ConvertCoreResponse;
use Puzzle\EventSubscriber\LoadComponent;
use Puzzle\EventSubscriber\RecordInstallScript;
use Puzzle\EventSubscriber\SeedDatabase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventServiceProvider implements ServiceProviderInterface
{
    private static array $subscribers = [
        BootstrapModule::class,
        LoadComponent::class,
        RecordInstallScript::class,
        SeedDatabase::class,
        ConvertCoreResponse::class
    ];

    public static function register(ContainerBuilder $container): void
    {
        $dispatcher = new EventDispatcher();
        foreach (static::$subscribers as $subscriber) {
            $dispatcher->addSubscriber(new $subscriber());
        }
        $container->set('event_dispatcher', $dispatcher);
    }
}
