<?php

namespace Puzzle\page\Controller\Admin;

use Puzzle\Http\InternalRedirectResponse;
use Puzzle\Http\TwigTemplateResponse;
use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\Request;

class PageController
{
    public function index(): TwigTemplateResponse
    {
        $pages = Page::orderBy('created_at', 'DESC')->get();
        return new TwigTemplateResponse(
            '@module_page/admin/index.html.twig',
            [
                'pages' => $pages
            ]
        );
    }

    public function create(): TwigTemplateResponse
    {
        return new TwigTemplateResponse('@module_page/admin/create.html.twig');
    }

    public function store(Request $request): InternalRedirectResponse
    {
        $page = Page::create($request->request->all());
        $page->save();

        return new InternalRedirectResponse('page.admin');
    }
}
