<?php

namespace Puzzle\file\EventSubscriber;

use Puzzle\file\Event\PreInsertFileEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TransliterateFileName implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PreInsertFileEvent::NAME => 'onPreInsertFile'
        ];
    }

    public function onPreInsertFile(PreInsertFileEvent $event): void
    {
        $slugger = new AsciiSlugger();
        $file = $event->getFile();

        $pathInfo = pathinfo($file['filename']);
        $baseName = $pathInfo['filename'];
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

        $transliteratedName = $slugger->slug($baseName)->lower();
        $filename = $transliteratedName . $extension;
        $file->setAttribute('filename', $filename);
    }
}
