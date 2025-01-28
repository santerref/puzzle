<?php

namespace Puzzle\EventSubscriber;

use Puzzle\Event\BootFinishedEvent;
use Puzzle\page\Entity\Page;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SeedDatabase implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            BootFinishedEvent::NAME => 'onBootFinished'
        ];
    }

    public function onBootFinished(BootFinishedEvent $event): void
    {
        $page = Page::create([
            'title' => 'Test page'
        ]);
        $page->save();
    }
}
