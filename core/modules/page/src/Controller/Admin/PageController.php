<?php

namespace Puzzle\page\Controller\Admin;

use Puzzle\Http\ResponseFactory;
use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController
{
    public function __construct(protected ResponseFactory $responseFactory)
    {
    }

    public function index(): Response
    {
        $pages = Page::orderBy('created_at', 'DESC')->get();
        return $this->responseFactory->createTwigTemplateResponse(
            '@module_page/admin/index.html.twig',
            [
                'pages' => $pages
            ]
        );
    }

    public function create(): Response
    {
        return $this->responseFactory->createTwigTemplateResponse('@module_page/admin/create.html.twig');
    }

    public function edit(Page $page): Response
    {
        return $this->responseFactory->createTwigTemplateResponse(
            '@module_page/admin/edit.html.twig',
            [
                'page' => $page
            ]
        );
    }

    public function update(Page $page, Request $request): Response
    {
        $page->update($request->request->all());
        return $this->responseFactory->createInternalRedirectResponse('page.admin');
    }

    public function destroy(Page $page): Response
    {
        $page->delete();
        return $this->responseFactory->createInternalRedirectResponse('page.admin');
    }

    public function store(Request $request): Response
    {
        $page = Page::create($request->request->all());
        $page->save();

        return $this->responseFactory->createInternalRedirectResponse('page.admin');
    }
}
