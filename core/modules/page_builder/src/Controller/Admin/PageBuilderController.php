<?php

namespace Puzzle\page_builder\Controller\Admin;

use Puzzle\Http\ResponseFactory;
use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\Response;

class PageBuilderController
{
    public function __construct(protected ResponseFactory $responseFactory)
    {
    }

    public function launch(string $uuid): Response
    {
        return $this->responseFactory->createTwigTemplateResponse(
            '@module_page_builder/page-builder.html.twig',
            [
                'page' => Page::find($uuid)
            ]
        );
    }
}
