<?php

namespace Puzzle\page\Controller;

use Puzzle\Http\ResponseFactory;
use Puzzle\page\Entity\Page;
use Puzzle\page\Event\AssetsEvent;
use Puzzle\page\Event\CssVariablesEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

class PageController
{
    public function __construct(
        protected ResponseFactory $responseFactory,
        protected EventDispatcher $eventDispatcher
    ) {
    }

    public function show(string $slug): Response
    {
        $headAssetsEvent = new AssetsEvent(AssetsEvent::HEAD);
        $this->eventDispatcher->dispatch($headAssetsEvent, AssetsEvent::NAME);

        $footerAssetsEvent = new AssetsEvent(AssetsEvent::FOOTER);
        $this->eventDispatcher->dispatch($footerAssetsEvent, AssetsEvent::NAME);

        $cssVariables = new CssVariablesEvent();
        $this->eventDispatcher->dispatch($cssVariables, CssVariablesEvent::NAME);

        return $this->responseFactory->createTwigTemplateResponse(
            '@module_page/page.html.twig',
            [
                'page' => Page::where('slug', $slug)->with([
                    'components' => function ($query) {
                        $query->whereNull('parent')->orderBy('weight');
                    }
                ])->first(),
                'css_variables' => $cssVariables->getVariables(),
                'head_assets' => [
                    'links' => $headAssetsEvent->getLinks(),
                    'stylesheets' => $headAssetsEvent->getStylesheets(),
                    'scripts' => $headAssetsEvent->getScripts(),
                ],
                'footer_assets' => [
                    'scripts' => $footerAssetsEvent->getScripts(),
                ]
            ]
        );
    }
}
