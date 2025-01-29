<?php

namespace Puzzle\ServiceProvider;

use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class ServiceProvider implements ServiceProviderInterface
{
    public function __construct(protected ContainerBuilder $container)
    {
    }
}
