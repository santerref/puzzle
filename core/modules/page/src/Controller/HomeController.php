<?php

namespace Puzzle\page\Controller;

use Puzzle\Http\TwigTemplateResponse;

class HomeController
{
    public function welcome(): TwigTemplateResponse
    {
        return new TwigTemplateResponse('@module_page/welcome.html.twig');
    }
}
