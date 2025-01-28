<?php

namespace Puzzle\Module\Bootstrapper;

use Puzzle\Module\Module;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ModuleBootstrapperInterface
{
    public function bootstrap(Module $module, ContainerBuilder $container): void;
}
