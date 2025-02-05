<?php

namespace Puzzle\page\Controller;

use Puzzle\Http\ResponseFactory;
use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\Response;

class PageController
{
    public function __construct(protected ResponseFactory $responseFactory)
    {
    }

    public function show(string $slug): Response
    {
        return $this->responseFactory->createTwigTemplateResponse(
            '@module_page/page.html.twig',
            [
                'page' => Page::where('slug', $slug)->with([
                    'components' => function ($query) {
                        $query->whereNull('parent')->orderBy('weight');
                    }
                 ])->first()
            ]
        );
    }
}
