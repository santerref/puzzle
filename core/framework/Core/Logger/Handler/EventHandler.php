<?php

namespace Puzzle\Core\Logger\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Puzzle\Event\LogEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventHandler extends AbstractProcessingHandler
{
    public function __construct(
        protected EventDispatcher $eventDispatcher,
        int|string|Level $level = Level::Debug,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        $this->eventDispatcher->dispatch(new LogEvent($record), LogEvent::NAME);
    }
}
