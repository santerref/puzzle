<?php

namespace Puzzle\Listener;

use Puzzle\Event\BootFinishedEvent;
use Puzzle\Event\Event;
use Puzzle\Entity\Model;

class AssignStorageListener
{
    public function handle(Event $event): void
    {
        $container = $event->getContainer();

        if ($container->has('storage')) {
            Model::setStorage($container->get('storage'));
        }
    }
}
