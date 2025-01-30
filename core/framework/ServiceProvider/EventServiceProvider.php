<?php

namespace Puzzle\ServiceProvider;

use Puzzle\EventSubscriber\BootstrapModule;
use Puzzle\EventSubscriber\ConvertCoreResponse;
use Puzzle\EventSubscriber\LoadComponent;
use Puzzle\EventSubscriber\RecordInstallScript;
use Puzzle\EventSubscriber\SeedDatabase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventServiceProvider extends ServiceProvider
{
    private static array $subscribers = [
        BootstrapModule::class,
        LoadComponent::class,
        RecordInstallScript::class,
        ConvertCoreResponse::class,
    ];

    public function register(): void
    {
        $dispatcher = new EventDispatcher();
        foreach (static::$subscribers as $subscriber) {
            $dispatcher->addSubscriber(new $subscriber());
        }
        $this->container->set('event_dispatcher', $dispatcher);
    }
}
