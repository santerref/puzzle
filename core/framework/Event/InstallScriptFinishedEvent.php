<?php

namespace Puzzle\Event;

class InstallScriptFinishedEvent extends Event
{
    public const NAME = 'installer.install_script_finished';

    public function __construct(private readonly string $className)
    {
    }

    public function getClassName()
    {
        return $this->className;
    }
}
