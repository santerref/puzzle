<?php

namespace Puzzle\ServiceProvider;

use Puzzle\EventSubscriber\BootstrapModule;
use Puzzle\EventSubscriber\ConvertCoreResponse;
use Puzzle\EventSubscriber\LoadComponent;
use Puzzle\EventSubscriber\RecordInstallScript;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventServiceProvider extends ServiceProvider
{
    private static array $subscribers = [
        LoadComponent::class,
        RecordInstallScript::class,
        ConvertCoreResponse::class,
    ];

    public function register(): void
    {
        foreach (static::$subscribers as $subscriberClass) {
            $this->container->register($subscriberClass, $subscriberClass)
                ->setPublic(true);
        }

        $dispatcherDefinition = new Definition(EventDispatcher::class);
        foreach (static::$subscribers as $subscriberServiceId) {
            $dispatcherDefinition->addMethodCall('addSubscriber', [new Reference($subscriberServiceId)]);
        }

        $this->container->setDefinition('event_dispatcher', $dispatcherDefinition)
            ->setPublic(true);
        $this->container->setAlias(EventDispatcher::class, 'event_dispatcher')
            ->setPublic(true);
    }
}
