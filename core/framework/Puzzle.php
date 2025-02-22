<?php

namespace Puzzle;

use Puzzle\Core\Component\FieldType\FieldTypeInterface;
use Puzzle\Core\Config;
use Puzzle\Core\State;
use Puzzle\Exceptions\FieldTypeDoNotExistsException;
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

    public static function fieldType(string $fieldType): FieldTypeInterface
    {
        $serviceId = 'field_type.' . $fieldType;
        if (!static::$container->has($serviceId)) {
            throw new FieldTypeDoNotExistsException($fieldType);
        }

        return static::$container->get($serviceId);
    }
}
