<?php

namespace Puzzle\Event;

use Puzzle\Module\Module;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Contracts\EventDispatcher\Event;

class ModuleDiscovered extends Event
{
    public const NAME = 'module.discovered';

    public function __construct(
        private readonly Module $module,
        private readonly ContainerBuilder $container
    ) {
    }

    public function getModule(): Module
    {
        return $this->module;
    }

    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }
}
