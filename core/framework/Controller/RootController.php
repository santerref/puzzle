<?php

namespace Puzzle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RootController extends BaseController
{
    public function front(): Response
    {
        return $this->render('root.html.twig');
    }
}
