<?php

namespace Puzzle\page\Controller\Api;

use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\JsonResponse;

class PageController
{
    public function fetch(string $uuid): JsonResponse
    {
        $page = Page::find($uuid);
        return new JsonResponse($page->load([
            'components' => function ($query) {
                $query->whereNull('parent')->orderBy('weight');
            }
        ]));
    }
}
