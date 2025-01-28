<?php

namespace Puzzle\EventSubscriber;

use Puzzle\Event\InstallScriptFinishedEvent;
use Puzzle\Storage\Database;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecordInstallScript implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            InstallScriptFinishedEvent::NAME => 'onInstallScriptFinished'
        ];
    }

    public function onInstallScriptFinished(InstallScriptFinishedEvent $event): void
    {
        $className = $event->getClassName();
        Database::table('installer_scripts')->insert(['class_name' => $className]);
    }
}
