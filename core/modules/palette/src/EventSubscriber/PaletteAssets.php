<?php

namespace Puzzle\palette\EventSubscriber;

use Puzzle\Core\Asset\Link;
use Puzzle\Core\Asset\Stylesheet;
use Puzzle\Core\Registry;
use Puzzle\page\Event\PagePreloadEvent;
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
            PagePreloadEvent::NAME => ['onPagePreload', 1000]
        ];
    }

    public function onPagePreload(PagePreloadEvent $event): void
    {
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
        $event->addCssVariables($this->getCssVariables());
    }

    protected function getCssVariables(): array
    {
        $variables = [];
        $palette = $this->paletteRegistry->get(Puzzle::config()->get('puzzle.palette'));

        foreach ($palette->get('colors', []) as $code => $meta) {
            $variables['color-' . $code] = $meta['code'];
        }

        $quoteSettings = [
            'font-family',
            'mono-font-family'
        ];

        $settings = [
            'font-family',
            'mono-font-family',
            'button-radius'
        ];

        foreach ($settings as $setting) {
            if ($palette->has('settings.' . $setting)) {
                if (in_array($setting, $quoteSettings)) {
                    $variables[$setting] = '"' . $palette->get('settings.' . $setting) . '"';
                } else {
                    $variables[$setting] = $palette->get('settings.' . $setting);
                }
            }
        }

        return $variables;
    }
}
