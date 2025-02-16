<?php

namespace Puzzle;

use Puzzle\Core\Config;
use Puzzle\core\State;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Puzzle
{
    private static ?ContainerBuilder $container = null;

    public static function setContainer(ContainerBuilder $container): void
    {
        self::$container = $container;
    }

    public static function config(): Config
    {
        return self::$container->get('config');
    }

    public static function state(): State
    {
        return self::$container->get('state');
    }
}
