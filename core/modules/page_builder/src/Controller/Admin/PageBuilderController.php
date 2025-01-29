<?php

namespace Puzzle\page_builder\Controller;

use Puzzle\Http\TwigTemplateResponse;
use Puzzle\page\Entity\Page;

class PageBuilderController
{
    public function launch(string $uuid): TwigTemplateResponse
    {
        $page = Page::findOrFail($uuid);
        return new TwigTemplateResponse('@page_builder/page-builder.html.twig', [
            'page_uuid' => $page->id
        ]);
    }
}
