<?php

namespace Puzzle\ServiceProvider;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ServiceProviderInterface
{
    public static function register(ContainerBuilder $container): void;
}
