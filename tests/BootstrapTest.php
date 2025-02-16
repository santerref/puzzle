<?php

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Puzzle\Bootstrap;
use Puzzle\Core\Component\ComponentDiscovery;
use Puzzle\Core\Component\Renderer;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(Bootstrap::class)]
class BootstrapTest extends TestCase
{
    public function testBoot()
    {
        $container = Bootstrap::boot();
        $this->assertInstanceOf(ContainerBuilder::class, $container);
        $this->assertTrue($container->has('event_dispatcher'));
        $this->assertTrue($container->has('service_container'));
        $this->assertTrue($container->has('router.route_collection'));
        $this->assertTrue($container->has('router'));
        $this->assertTrue($container->has('request.context'));
        $this->assertTrue($container->has('component_discovery'));
        $this->assertTrue($container->has('component.renderer'));
        $this->assertTrue($container->has(ComponentDiscovery::class));
        $this->assertTrue($container->has(Renderer::class));
        $this->assertTrue($container->has('twig'));
    }
}
