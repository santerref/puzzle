<?php

namespace Puzzle\ThirdParty\Twig;

use Puzzle\page_builder\Entity\Component;
use Puzzle\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PuzzleExtension extends AbstractExtension
{
    public function __construct(protected ContainerBuilder $container)
    {
    }

    public function getFilters()
    {
        return [
            new TwigFilter('css', [$this, 'css']),
            new TwigFilter('image_url', [$this, 'imageUrl'])
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('dump', [$this, 'dump']),
            new TwigFunction('dd', [$this, 'dd']),
            new TwigFunction('route', [$this, 'route']),
            new TwigFunction('render', [$this, 'render'], ['is_safe' => ['html']]),
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

    public function children($children)
    {
        //@TODO: Check if context of page builder or not.l
        if (true) {
            return '<Children/>';
        }
    }

    public function dump($arg)
    {
        dump($arg);
    }

    public function render(Component $component)
    {
        $components = $this->container->get('component_discovery')->getComponents();
        return $this->container->get('component.renderer')->render(
            $components[$component->component_type],
            $component,
            [
                'component' => $component
            ]
        );
    }

    public function dd($arg)
    {
        dd($arg);
    }

    public function route($route, $args = [])
    {
        $urlGenerator = $this->container->get('router.url_generator');
        return $urlGenerator->generate($route, $args);
    }

    public function imageUrl($id)
    {
        if (filter_var($id, FILTER_VALIDATE_URL) !== false) {
            return $id;
        }
        $image = File::find($id);
        return 'https://puzzle.ddev.site' . $image->path;
    }
}
