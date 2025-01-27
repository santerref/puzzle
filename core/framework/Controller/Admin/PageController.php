<?php

namespace Puzzle\Controller\Admin;

use Puzzle\Controller\BaseController;
use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends BaseController
{
    public function index(): Response
    {
        return $this->render('admin/pages/index.html.twig');
    }

    public function create(): Response
    {
        return $this->render('admin/pages/create.html.twig');
    }

    public function store(Request $request): Response
    {
        $page = Page::create($request->request->all());
        $page->save();

        return $this->redirect('admin.pages');
    }
}
