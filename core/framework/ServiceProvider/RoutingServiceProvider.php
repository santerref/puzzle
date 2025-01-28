<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Routing\Controller\BaseController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class RoutingServiceProvider implements ServiceProviderInterface
{
    public static function register(ContainerBuilder $container): void
    {
        $fileLocator = new FileLocator([
            PUZZLE_ROOT . '/core/config',
        ]);
        $routeLoader = new YamlFileLoader($fileLocator);

        $routeCollectionDefinition = new Definition(RouteCollection::class);
        $container->setDefinition('router.route_collection', $routeCollectionDefinition)
            ->setFactory([$routeLoader, 'load'])
            ->setArguments(['routes.yaml'])
            ->setPublic(true);

        $container->register('router', UrlMatcher::class)
            ->setArguments([
                new Reference('router.route_collection'),
                new Reference('request.context'),
            ])
            ->setPublic(true);

        $request = Request::createFromGlobals();
        $context = new RequestContext();
        $context->fromRequest($request);
        $container->set('request.context', $context);

        $urlGeneratorDefinition = new Definition(UrlGenerator::class, [
            $container->get('router.route_collection'),
            $container->get('request.context')
        ]);
        $container->setDefinition('router.url_generator', $urlGeneratorDefinition)
            ->setPublic(true);
    }
}
