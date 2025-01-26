<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Listener\LoadComponentListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class EventServiceProvider implements ServiceProvider
{
    private static array $listeners = [
        [
            'event' => 'app.boot_finished',
            'listener' => LoadComponentListener::class,
            'method' => 'onBootFinished'
        ]
    ];

    public static function register(ContainerBuilder $container): void
    {
        $dispatcher = new EventDispatcher();
        foreach (static::$listeners as $listenerConfig) {
            $listener = new $listenerConfig['listener']();
            $dispatcher->addListener($listenerConfig['event'], [$listener, $listenerConfig['method']]);
        }
        $container->set('event_dispatcher', $dispatcher);
    }
}
