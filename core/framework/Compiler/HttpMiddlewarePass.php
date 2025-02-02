<?php

namespace Puzzle\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HttpMiddlewarePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $middlewareRegistryDefinition = $container->getDefinition('http.middleware_registry');

        $middlewareServices = $container->findTaggedServiceIds('request.middleware');
        foreach ($middlewareServices as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $alias = $tag['alias'] ?? $serviceId;
                $middlewareRegistryDefinition->addMethodCall('add', [
                    $alias,
                    new Reference($serviceId)
                ]);
            }
        }
    }
}
