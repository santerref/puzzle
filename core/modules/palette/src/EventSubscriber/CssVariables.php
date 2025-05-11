<?php

namespace Puzzle\palette\EventSubscriber;

use Puzzle\Core\Registry;
use Puzzle\page\Event\CssVariablesEvent;
use Puzzle\Puzzle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CssVariables implements EventSubscriberInterface
{
    public function __construct(protected Registry $paletteRegistry)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CssVariablesEvent::NAME => ['onCssVariables', 1000]
        ];
    }

    public function onCssVariables(CssVariablesEvent $event)
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

        $event->addVariables($variables);
    }
}
