<?php

namespace Puzzle\Listener;

use Puzzle\Event\Event;
use Puzzle\page\Entity\Page;
use Puzzle\Storage\Database;

class SeedDatabaseListener implements ListenerInterface
{
    public function handle(Event $event): void
    {
        $page = Page::create([
            'title' => 'Test page'
        ]);
        $page->save();
    }
}
