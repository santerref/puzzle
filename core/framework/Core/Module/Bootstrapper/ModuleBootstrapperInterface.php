<?php

namespace Puzzle\Core\Module\Bootstrapper;

use Puzzle\Core\Module\Module;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ModuleBootstrapperInterface
{
    public function bootstrap(Module $module, ContainerBuilder $container): void;
}
