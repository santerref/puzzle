<?php

namespace Puzzle;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as ServicesYamlFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class App
{
    protected FileLocatorInterface $fileLocator;

    protected RouteCollection $routes;

    protected ContainerBuilder $container;

    public function __construct()
    {
        $this->fileLocator = new FileLocator([
            __DIR__ . '/../config'
        ]);
        $this->container = new ContainerBuilder();
        $this->loadRoutes();
        $this->loadServices();
        $this->registerTwig();
        $this->container->compile();
    }

    public function run(): void
    {
        $request = Request::createFromGlobals();
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($this->routes, $context);

        $controllerResolver = new ContainerControllerResolver($this->container);
        $argumentResolver = new ArgumentResolver();

        try {
            $request->attributes->add($matcher->match($request->getPathInfo()));

            $controller = $controllerResolver->getController($request);
            $arguments = $argumentResolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not Found', 404);
        } catch (Exception $e) {
            $response = new Response('Error', 500);
        }

        $response->send();
    }

    protected function loadRoutes(): void
    {
        $loader = new YamlFileLoader($this->fileLocator);
        $this->routes = $loader->load('routes.yaml');
    }

    protected function loadServices(): void
    {
        $loader = new ServicesYamlFileLoader($this->container, $this->fileLocator);
        $loader->load('services.yaml');
    }

    protected function registerTwig(): void
    {
        $loader = new FilesystemLoader(__DIR__ . '/../resources/templates');
        $this->container->register('twig', Environment::class)
            ->addArgument($loader)
            ->addArgument([
                'cache' => false
            ])
            ->setPublic(true);
    }

}
