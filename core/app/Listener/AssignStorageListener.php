<?php

namespace Puzzle\Listener;

use Puzzle\Event\BootFinishedEvent;
use Puzzle\Model\Model;

class AssignStorageListener
{
    public function onBootFinished(BootFinishedEvent $event)
    {
        $container = $event->getContainer();

        if ($container->has('storage')) {
            Model::setStorage($container->get('storage'));
        }
    }
}
