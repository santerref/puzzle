<?php

namespace Puzzle\ServiceProvider;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class RoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $request = Request::createFromGlobals();
        $this->container->set('request', $request);

        $routeCollectionDefinition = new Definition(RouteCollection::class);
        $this->container->setDefinition('router.route_collection', $routeCollectionDefinition)
            ->setPublic(true);

        $this->container->register('router', UrlMatcher::class)
            ->setArguments([
                new Reference('router.route_collection'),
                new Reference('request.context'),
            ])
            ->setPublic(true);

        $request = Request::createFromGlobals();
        $context = new RequestContext();
        $context->fromRequest($request);
        $this->container->set('request.context', $context);

        $urlGeneratorDefinition = new Definition(UrlGenerator::class, [
            new Reference('router.route_collection'),
            new Reference('request.context'),
        ]);
        $this->container->setDefinition('router.url_generator', $urlGeneratorDefinition)
            ->setPublic(true);
    }
}
