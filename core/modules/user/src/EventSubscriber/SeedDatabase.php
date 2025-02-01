<?php

namespace Puzzle\user\EventSubscriber;

use Puzzle\Event\InstallerFinishedEvent;
use Puzzle\user\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SeedDatabase implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            InstallerFinishedEvent::NAME => 'onInstallerFinished'
        ];
    }

    public function onInstallerFinished(InstallerFinishedEvent $event): void
    {
        $container = $event->getContainer();
        $hasher = $container->get('password_hasher');

        $user = User::create([
            'email' => 'john.doe@example.com',
            'password' => $hasher->hash('admin'),
        ]);
        $user->save();
    }
}
