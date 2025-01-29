<?php

namespace Puzzle\page\Controller;

use Puzzle\Http\TwigTemplateResponse;

class HomeController
{
    public function welcome(): TwigTemplateResponse
    {
        return new TwigTemplateResponse('@page/welcome.html.twig');
    }
}
