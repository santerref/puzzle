<?php

namespace Puzzle\EventSubscriber;

use Puzzle\Event\BootFinishedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoadComponent implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            BootFinishedEvent::NAME => 'onBootFinished',
        ];
    }

    public function onBootFinished(BootFinishedEvent $event): void
    {
        $container = $event->getContainer();

        if ($container->has('component_discovery')) {
            $componentDiscovery = $container->get('component_discovery');
            $componentDiscovery->discover();
        }
    }
}
