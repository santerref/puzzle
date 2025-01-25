<?php

namespace Puzzle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as KernelControllerResolver;

class ControllerResolver extends KernelControllerResolver
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function instantiateController(string $class): object
    {
        if ($this->container->has($class)) {
            return $this->container->get($class);
        }

        return parent::instantiateController($class);
    }
}
