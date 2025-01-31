<?php

namespace Puzzle\page\EventSubscriber;

use Puzzle\Event\InstallerFinishedEvent;
use Puzzle\page\Entity\Page;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SeedDatabase implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            InstallerFinishedEvent::NAME => 'onInstallerFinished'
        ];
    }

    public function onInstallerFinished(InstallerFinishedEvent $event): void
    {
        $page = Page::create([
            'title' => 'Test page',
            'slug' => 'test-page'
        ]);
        $page->save();
    }
}
