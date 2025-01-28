<?php

namespace Puzzle\EventSubscriber;

use Puzzle\Event\ModuleDiscovered;
use Puzzle\Module\Bootstrapper\RoutingBootstrapper;
use Puzzle\Module\Bootstrapper\ServiceBootstrapper;
use Puzzle\Module\Bootstrapper\TemplateBootstrapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BootstrapModule implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ModuleDiscovered::NAME => 'onModuleDiscovered'
        ];
    }

    public function onModuleDiscovered(ModuleDiscovered $event)
    {
        $bootstrappers = [
            new ServiceBootstrapper(),
            new RoutingBootstrapper(),
            new TemplateBootstrapper()
        ];

        foreach ($bootstrappers as $bootstrapper) {
            $bootstrapper->bootstrap(
                $event->getModule(),
                $event->getContainer()
            );
        }
    }
}
