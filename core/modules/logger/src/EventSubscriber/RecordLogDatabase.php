<?php

namespace Puzzle\logger\EventSubscriber;

use Puzzle\Event\LogEvent;
use Puzzle\logger\Entity\Log;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecordLogDatabase implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            LogEvent::NAME => 'onLogWrite'
        ];
    }

    public function onLogWrite(LogEvent $event): void
    {
        Log::create($event->getRecord()->toArray());
    }
}
