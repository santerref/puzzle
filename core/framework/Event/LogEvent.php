<?php

namespace Puzzle\Event;

use Monolog\LogRecord;
use Symfony\Contracts\EventDispatcher\Event;

class LogEvent extends Event
{
    public const NAME = 'logger.write';

    public function __construct(protected LogRecord $record)
    {
    }

    public function getRecord(): LogRecord
    {
        return $this->record;
    }
}
