<?php

namespace Puzzle\Listener;

use Puzzle\Event\BootFinishedEvent;
use Puzzle\Event\Event;

class LoadComponentListener implements ListenerInterface
{
    public function handle(Event $event): void
    {
        $container = $event->getContainer();

        if ($container->has('component_discovery')) {
            $componentDiscovery = $container->get('component_discovery');
            $componentDiscovery->discover();
        }
    }
}
