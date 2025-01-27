<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Event\BootFinishedEvent;
use Puzzle\Event\InstallScriptFinishedEvent;
use Puzzle\Listener\AssignStorageListener;
use Puzzle\Listener\LoadComponentListener;
use Puzzle\Listener\RecordInstallScriptListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventServiceProvider implements ServiceProviderInterface
{
    private static array $listeners = [
        [
            'event' => BootFinishedEvent::NAME,
            'listener' => LoadComponentListener::class,
        ],
        [
            'event' => BootFinishedEvent::NAME,
            'listener' => AssignStorageListener::class,
        ],
        [
            'event' => InstallScriptFinishedEvent::NAME,
            'listener' => RecordInstallScriptListener::class,
        ]
    ];

    public static function register(ContainerBuilder $container): void
    {
        $dispatcher = new EventDispatcher();
        foreach (static::$listeners as $listenerConfig) {
            $listener = new $listenerConfig['listener']();
            $dispatcher->addListener($listenerConfig['event'], [$listener, 'handle']);
        }
        $container->set('event_dispatcher', $dispatcher);
    }
}
