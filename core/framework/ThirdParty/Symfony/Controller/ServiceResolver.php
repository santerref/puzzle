<?php

namespace Puzzle\ThirdParty\Symfony\Controller;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ServiceResolver implements ValueResolverInterface
{
    public function __construct(
        protected ContainerInterface $container
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        // Check if the argument type exists and is a service
        if (class_exists($type) && $this->container->has($type)) {
            return [$this->container->get($type)];
        }

        return [];
    }
}
