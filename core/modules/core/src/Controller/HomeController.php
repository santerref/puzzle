<?php

namespace Puzzle\core\Controller;

use Puzzle\Http\TwigTemplateResponse;

class HomeController
{
    public function welcome(): TwigTemplateResponse
    {
        return new TwigTemplateResponse('@module_core/welcome.html.twig');
    }
}
