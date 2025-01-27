<?php

namespace Puzzle\Controller;

use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\Response;

class RootController extends BaseController
{
    public function front(): Response
    {
        $page = Page::first();
        return $this->render('root.html.twig', ['page_id' => $page->id]);
    }
}
