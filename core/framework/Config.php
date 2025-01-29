<?php

namespace Puzzle;

use Illuminate\Support\Arr;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Config
{
    private static ?ContainerBuilder $container = null;

    public static function setContainer(ContainerBuilder $container): void
    {
        self::$container = $container;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $config = self::$container->getParameter('config') ?? [];
        return Arr::get($config, $key, $default);
    }
}
