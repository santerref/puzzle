<?php

namespace Puzzle\Controller;

use Puzzle\Entity\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig\Environment;

abstract class BaseController
{
    public function __construct(protected Environment $twig, protected UrlGenerator $urlGenerator)
    {
    }

    protected function render(string $template, array $args = []): Response
    {
        $content = $this->twig->render($template, $args);
        $response = new Response($content);

        return $response;
    }

    protected function json(array|Entity $data): Response
    {
        return new JsonResponse($data);
    }

    protected function redirect($route, $status = 302): Response
    {
        $url = $this->urlGenerator->generate($route);
        return new RedirectResponse($url, $status);
    }
}
