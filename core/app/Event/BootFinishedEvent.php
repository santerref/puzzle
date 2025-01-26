<?php

namespace Puzzle\Event;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Contracts\EventDispatcher\Event;

class BootFinishedEvent extends Event
{
    public const NAME = 'app.boot_finished';

    public function __construct(private readonly ContainerBuilder $container)
    {
    }

    public function getContainer()
    {
        return $this->container;
    }
}
