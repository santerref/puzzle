<?php

namespace Puzzle\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigExtensionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $twig = $container->get('twig');

        $taggedServices = $container->findTaggedServiceIds('twig.twig_extension');
        foreach ($taggedServices as $id => $tags) {
            $twig->addExtension($container->get($id));
        }
    }
}
