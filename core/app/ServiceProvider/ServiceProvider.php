<?php

namespace Puzzle\ServiceProvider;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ServiceProvider
{
    public static function register(ContainerBuilder $container): void;
}
