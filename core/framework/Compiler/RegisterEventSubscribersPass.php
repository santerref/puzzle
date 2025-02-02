<?php

namespace Puzzle\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterEventSubscribersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $eventDispatcher = $container->get('event_dispatcher');
        foreach ($container->findTaggedServiceIds('event.event_subscriber') as $id => $attributes) {
            $container->getDefinition($id)->setPublic(true);
            $eventDispatcher->addSubscriber($container->get($id));
        }
    }
}
