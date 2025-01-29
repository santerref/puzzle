<?php

namespace Puzzle\ServiceProvider;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ServiceProviderInterface
{
    public function register(): void;
}
