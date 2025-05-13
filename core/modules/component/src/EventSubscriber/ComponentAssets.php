<?php

namespace Puzzle\component\EventSubscriber;

use Puzzle\Core\Asset\Script;
use Puzzle\Core\Asset\Stylesheet;
use Puzzle\Core\Registry;
use Puzzle\page\Event\PagePreloadEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ComponentAssets implements EventSubscriberInterface
{
    public function __construct(protected Registry $componentRegistry)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PagePreloadEvent::NAME => ['onPagePreload', 250]
        ];
    }

    public function onPagePreload(PagePreloadEvent $event): void
    {
        foreach ($event->getComponentTypes() as $componentTypeName) {
            $componentType = $this->componentRegistry->get($componentTypeName);
            $namespace = 'component_' . $componentTypeName;
            foreach ($componentType->getStylesheets() as $href => $attributes) {
                $event->addStylesheet(new Stylesheet($href, $attributes, $namespace));
            }
            foreach ($componentType->getHeadScripts() as $src => $attributes) {
                $event->addHeadScript(new Script($src, $attributes, $namespace));
            }
            foreach ($componentType->getFooterScripts() as $src => $attributes) {
                $event->addFooterScript(new Script($src, $attributes, $namespace));
            }
        }
    }
}
