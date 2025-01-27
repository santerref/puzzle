<?php

namespace Puzzle\ThirdParty\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PuzzleExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('css', [$this, 'css']),
        ];
    }

    public function css($classes)
    {
        if (is_array($classes)) {
            $classes = array_map(function ($class) {
                return trim($class);
            }, $classes);
            $classes = array_values(array_unique($classes));
            $classes = implode(' ', $classes);
        }

        return trim($classes);
    }
}
