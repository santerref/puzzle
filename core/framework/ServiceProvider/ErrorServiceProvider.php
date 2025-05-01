<?php

namespace Puzzle\ServiceProvider;

use Puzzle\Puzzle;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class ErrorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (Puzzle::config()->get('puzzle.env') == 'local' && Puzzle::config()->get('puzzle.debug')) {
            if (class_exists('Whoops\Run')) {
                $whoops = new Run();
                $whoops->pushHandler(new PrettyPageHandler());
                $whoops->register();
            }
        } else {
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        }
    }
}
