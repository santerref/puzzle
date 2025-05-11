<?php

namespace Puzzle\palette\EventSubscriber;

use Puzzle\Core\Asset\Link;
use Puzzle\Core\Asset\Stylesheet;
use Puzzle\Core\Registry;
use Puzzle\page\Event\AssetsEvent;
use Puzzle\Puzzle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaletteAssets implements EventSubscriberInterface
{
    public function __construct(protected Registry $paletteRegistry)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AssetsEvent::NAME => ['onAssets', 1000]
        ];
    }

    public function onAssets(AssetsEvent $event): void
    {
        if ($event->getLocation() == AssetsEvent::HEAD) {
            $googlePreconnect = false;
            $palette = $this->paletteRegistry->get(Puzzle::config()->get('puzzle.palette'));
            foreach ($palette->get('fonts', []) as $font) {
                $urlInfo = parse_url($font['url']);
                if ($urlInfo['host'] == 'fonts.googleapis.com' && !$googlePreconnect) {
                    $event->addLink(new Link('preconnect', ['href' => 'https://fonts.googleapis.com']));
                    $event->addLink(
                        new Link('preconnect', ['href' => 'https://fonts.gstatic.com', 'crossorigin' => false])
                    );
                    $googlePreconnect = true;
                }
                $event->addStylesheet(new Stylesheet($font['url']));
            }
        }
    }
}
