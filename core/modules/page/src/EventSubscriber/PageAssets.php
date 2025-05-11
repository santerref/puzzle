<?php

namespace Puzzle\page\EventSubscriber;

use Puzzle\Core\Asset\Stylesheet;
use Puzzle\page\Event\AssetsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PageAssets implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AssetsEvent::NAME => ['onAssets', 500]
        ];
    }

    public function onAssets(AssetsEvent $event): void
    {
        if ($event->getLocation() == AssetsEvent::HEAD) {
            $event->addStylesheet(new Stylesheet('css/reset.scss', [], 'module_page'));
        }
    }
}
