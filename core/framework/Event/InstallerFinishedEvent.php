<?php

namespace Puzzle\Event;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Contracts\EventDispatcher\Event;

class InstallerFinishedEvent extends Event
{
    public const NAME = 'installer.finished';

    public function __construct(private ContainerBuilder $container)
    {
    }

    public function getContainer()
    {
        return $this->container;
    }
}
