<?php

namespace Puzzle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class InstallerFinishedEvent extends Event
{
    public const NAME = 'installer.finished';
}
