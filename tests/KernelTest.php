<?php

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Puzzle\Bootstrap;
use Puzzle\Event\BootFinishedEvent;
use Puzzle\Kernel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[CoversClass(Kernel::class)]
class KernelTest extends TestCase
{
    public function testInit()
    {
        $container = Bootstrap::boot();
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(BootFinishedEvent::class),
                BootFinishedEvent::NAME
            );
        $container->set('event_dispatcher', $dispatcher);
        $kernel = new Kernel($container);
        $kernel->init();
    }
}
