<?php

namespace Puzzle\page_builder\Controller;

use Puzzle\Http\TwigTemplateResponse;
use Puzzle\page\Entity\Page;

class RootController
{
    public function front(): TwigTemplateResponse
    {
        $page = Page::first();
        return new TwigTemplateResponse('@page_builder/root.html.twig', ['page_id' => $page->id]);
    }
}
