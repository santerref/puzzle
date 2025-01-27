<?php

namespace Puzzle\Listener;

use Puzzle\Event\Event;

interface ListenerInterface
{
    public function handle(Event $event): void;
}
