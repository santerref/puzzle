<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Http\MiddlewareRegistry;
use Puzzle\Http\ResponseFactory;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class HttpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $responseFactoryDefinition = new Definition(ResponseFactory::class, [
            new Reference('twig'),
            new Reference('router.url_generator')
        ]);
        $this->container->setDefinition('http.response_factory', $responseFactoryDefinition)
            ->setPublic(true);
        $this->container->setAlias(ResponseFactory::class, 'http.response_factory')
            ->setPublic(true);
        $middlewareRegistryDefinition = new Definition(MiddlewareRegistry::class);
        $this->container->setDefinition('http.middleware_registry', $middlewareRegistryDefinition)
            ->setPublic(true);
        $this->container->setAlias(MiddlewareRegistry::class, 'http.middleware_registry');
    }
}
