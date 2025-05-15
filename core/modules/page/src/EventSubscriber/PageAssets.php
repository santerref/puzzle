<?php

namespace Puzzle\page\EventSubscriber;

use Puzzle\Core\Asset\Stylesheet;
use Puzzle\page\Event\PagePreloadEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PageAssets implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PagePreloadEvent::NAME => ['onPagePreload', 500]
        ];
    }

    public function onPagePreload(PagePreloadEvent $event): void
    {
        $event->addStylesheet(new Stylesheet('css/tailwind.css', [], 'module_page'));
    }
}
