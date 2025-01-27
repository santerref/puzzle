<?php

namespace Puzzle\Controller\Api;

use Puzzle\Controller\BaseController;
use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\Response;

class PageController extends BaseController
{
    public function fetch(string $uuid): Response
    {
        return $this->json(Page::find($uuid));
    }
}
