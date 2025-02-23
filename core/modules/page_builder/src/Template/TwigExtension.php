<?php

namespace Puzzle\page_builder\Template;

use Illuminate\Support\Arr;
use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly Request $request,
        private readonly UrlMatcher $router,
        private readonly RouteCollection $routeCollection
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'page_builder' => $this->isPageBuilderRoute(),
            'page' => $this->getCurrentPage(),
        ];
    }

    private function isPageBuilderRoute(): bool
    {
        $routeInfo = $this->router->match($this->request->getPathInfo());
        $route = $this->routeCollection->get($routeInfo['_route']);
        return $route->getOption('page_builder') ?? false;
    }

    private function getCurrentPage(): ?Page
    {
        $routeInfo = $this->router->match($this->request->getPathInfo());
        $page = null;

        if (Arr::get($routeInfo, '_route') == 'page.show') {
            $page = Page::where('slug', $routeInfo['slug'])->first();
        }

        if ($this->request->headers->has('X-Puzzle-Page-Uuid')) {
            $page = Page::find($this->request->headers->get('X-Puzzle-Page-Uuid'));
        }

        return $page;
    }
}
