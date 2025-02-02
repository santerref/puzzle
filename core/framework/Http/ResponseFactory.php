<?php

namespace Puzzle\Http;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig\Environment;

class ResponseFactory
{
    public function __construct(
        protected Environment $twig,
        protected UrlGenerator $urlGenerator
    ) {
    }

    public function createInternalRedirectResponse(
        string $route,
        array $parameters = [],
        int $status = 302
    ): Response {
        $url = $this->urlGenerator->generate($route, $parameters);
        return new RedirectResponse($url, $status);
    }

    public function createTwigTemplateResponse(string $template, array $args = []): Response
    {
        $content = $this->twig->render($template, $args);
        return new Response($content);
    }
}
