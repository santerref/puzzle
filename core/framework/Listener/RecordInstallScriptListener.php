<?php

namespace Puzzle\Listener;

use Puzzle\Event\Event;
use Puzzle\Storage\Database;

class RecordInstallScriptListener implements ListenerInterface
{
    public function handle(Event $event): void
    {
        $className = $event->getClassName();
        Database::table('installer_scripts')->insert(['class_name' => $className]);
    }
}
