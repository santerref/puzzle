<?php

namespace Puzzle\page\Controller;

use Puzzle\Http\ResponseFactory;
use Puzzle\page\Entity\Page;
use Puzzle\page\Event\PagePreloadEvent;
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
        $page = Page::where('slug', $slug)->with([
            'components' => function ($query) {
                $query->whereNull('parent')->orderBy('weight');
            }
        ])->first();

        $components = $page->components->flatMap(function ($component) {
            return collect([$component])->merge($component->children);
        });
        $componentTypes = $components->pluck('component_type')->unique()->toArray();

        $pagePreloadEvent = new PagePreloadEvent($componentTypes);
        $this->eventDispatcher->dispatch($pagePreloadEvent, PagePreloadEvent::NAME);

        return $this->responseFactory->createTwigTemplateResponse(
            '@module_page/page.html.twig',
            [
                'page' => $page,
                'css_variables' => $pagePreloadEvent->getCssVariables(),
                'head_assets' => [
                    'links' => $pagePreloadEvent->getLinks(),
                    'stylesheets' => $pagePreloadEvent->getStylesheets(),
                    'scripts' => $pagePreloadEvent->getHeadScripts(),
                ],
                'footer_assets' => [
                    'scripts' => $pagePreloadEvent->getFooterScripts(),
                ]
            ]
        );
    }
}
