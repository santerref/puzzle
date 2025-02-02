<?php

namespace Puzzle\core\Controller;

use Puzzle\Http\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function __construct(protected ResponseFactory $responseFactory)
    {
    }

    public function welcome(): Response
    {
        return $this->responseFactory->createTwigTemplateResponse('@module_core/welcome.html.twig');
    }
}
