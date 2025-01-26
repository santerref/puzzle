<?php

namespace Puzzle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

abstract class BaseController
{
    public function __construct(protected Environment $twig)
    {
    }

    protected function render(string $template, array $args = []): Response
    {
//        try {
        $content = $this->twig->render($template, $args);
        $response = new Response($content);
        /* } catch (\Exception $e) {
             $response = new Response('Error', 500);
         }*/

        return $response;
    }

    protected function json(array $data)
    {
        return new JsonResponse($data);
    }
}
