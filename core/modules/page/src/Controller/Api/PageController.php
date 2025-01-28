<?php

namespace Puzzle\page\Controller\Api;

use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\JsonResponse;

class PageController
{
    public function fetch(string $uuid): JsonResponse
    {
        return new JsonResponse(Page::find($uuid));
    }
}
