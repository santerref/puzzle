<?php

namespace Puzzle\page\Controller;

use Puzzle\Http\TwigTemplateResponse;
use Puzzle\page\Entity\Page;

class PageController
{
    public function show(string $slug): TwigTemplateResponse
    {
        return new TwigTemplateResponse(
            '@module_page/page.html.twig',
            [
                'page' => Page::where('slug', $slug)->first()
            ]
        );
    }
}
