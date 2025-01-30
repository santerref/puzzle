<?php

namespace Puzzle\page_builder\Controller\Admin;

use Puzzle\Http\TwigTemplateResponse;

class PageBuilderController
{
    public function launch(string $uuid): TwigTemplateResponse
    {
        return new TwigTemplateResponse('@module_page_builder/page-builder.html.twig', [
            'page_uuid' => $uuid
        ]);
    }
}
