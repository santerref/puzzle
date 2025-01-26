<?php

namespace Puzzle\Listener;

use Puzzle\Event\BootFinishedEvent;

class LoadComponentListener
{
    public function onBootFinished(BootFinishedEvent $event)
    {
        $container = $event->getContainer();

        if ($container->has('component_discovery')) {
            $componentDiscovery = $container->get('component_discovery');
            $componentDiscovery->discover();
        }
    }
}
