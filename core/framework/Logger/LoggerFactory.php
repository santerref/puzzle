<?php

namespace Puzzle\Logger;

use Monolog\Handler\FilterHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Puzzle\Config;
use Puzzle\Logger\Handler\EventHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LoggerFactory
{
    protected array $loggers;

    public function __construct(private ContainerBuilder $container)
    {
    }

    public function get($channel): LoggerInterface
    {
        if (isset($this->loggers[$channel])) {
            return $this->loggers[$channel];
        }

        $streamHandlers = [];
        $streamHandlers[] = new StreamHandler(
            PUZZLE_ROOT . '/var/logs/error.log',
            Level::Error
        );

        $infoStreamHandler = new StreamHandler(
            PUZZLE_ROOT . '/var/logs/info.log',
            Level::Info,
        );
        $streamHandlers[] = new FilterHandler($infoStreamHandler, Level::Info, Level::Warning);

        if (Config::get('puzzle.debug', false)) {
            $debugStreamHandler = new StreamHandler(
                PUZZLE_ROOT . '/var/logs/debug.log',
                Level::Debug,
                false
            );
            $streamHandlers[] = new FilterHandler($debugStreamHandler, Level::Debug, Level::Debug);
        }

        $streamHandlers[] = new EventHandler($this->container->get('event_dispatcher'), Level::Debug);

        $logger = new Logger($channel, $streamHandlers);
        $this->loggers[$channel] = $logger;

        return $logger;
    }
}
