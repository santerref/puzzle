<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Event\BootFinishedEvent;
use Puzzle\Event\InstallerFinishedEvent;
use Puzzle\Event\InstallScriptFinishedEvent;
use Puzzle\Listener\AssignStorageListener;
use Puzzle\Listener\LoadComponentListener;
use Puzzle\Listener\RecordInstallScriptListener;
use Puzzle\Listener\SeedDatabaseListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventServiceProvider implements ServiceProviderInterface
{
    private static array $listeners = [
        BootFinishedEvent::NAME => [
            LoadComponentListener::class,
            AssignStorageListener::class,
        ],
        InstallScriptFinishedEvent::NAME => [
            RecordInstallScriptListener::class,
        ],
        InstallerFinishedEvent::NAME => [
            SeedDatabaseListener::class,
        ]
    ];

    public static function register(ContainerBuilder $container): void
    {
        $dispatcher = new EventDispatcher();
        foreach (static::$listeners as $event => $listeners) {
            foreach ($listeners as $listener) {
                $dispatcher->addListener($event, [new $listener(), 'handle']);
            }
        }
        $container->set('event_dispatcher', $dispatcher);
    }
}
