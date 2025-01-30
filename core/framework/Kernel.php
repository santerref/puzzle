<?php

namespace Puzzle;

use Puzzle\Event\BootFinishedEvent;
use Puzzle\Event\ResponsePrepareEvent;
use Puzzle\ThirdParty\Symfony\Controller\EntityResolver;
use Puzzle\ThirdParty\Symfony\Controller\ServiceResolver;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestValueResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

readonly class Kernel
{
    public function __construct(private ContainerBuilder $container)
    {
    }

    public function init(): void
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(new BootFinishedEvent($this->container), BootFinishedEvent::NAME);
    }

    public function handle(Request $request): Response
    {
        $router = $this->container->get('router');
        $eventDispatcher = $this->container->get('event_dispatcher');

        $controllerResolver = new ContainerControllerResolver($this->container);
        $metadataFactory = new ArgumentMetadataFactory();
        $serviceValueResolver = new ServiceResolver($this->container);
        $argumentResolver = new ArgumentResolver(
            $metadataFactory,
            [
                new EntityResolver(),
                new RequestAttributeValueResolver(),
                new RequestValueResolver(),
                $serviceValueResolver
            ]
        );

        try {
            $request->attributes->add($router->match($request->getPathInfo()));

            $controller = $controllerResolver->getController($request);
            $arguments = $argumentResolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
            $event = new ResponsePrepareEvent($response, $this->container);
            $eventDispatcher->dispatch(
                $event,
                ResponsePrepareEvent::NAME
            );
            $response = $event->getResponse();
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not Found', 404);
        } catch (Exception $e) {
            $response = new Response('Error', 500);
        }

        return $response;
    }
}
