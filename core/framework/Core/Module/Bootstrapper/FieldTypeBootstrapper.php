<?php

namespace Puzzle\Core\Module\Bootstrapper;

use Puzzle\Core\Component\FieldType\FieldTypeInterface;
use Puzzle\Core\Module\Module;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

class FieldTypeBootstrapper implements ModuleBootstrapperInterface
{
    public function bootstrap(Module $module, ContainerBuilder $container): void
    {
        $fieldTypes = $container->hasParameter('field_types') ? $container->getParameter('field_types') : [];
        $finder = new Finder();

        try {
            $finder->files()->in($module->getPath() . '/src/FieldType')->name('*.php');

            foreach ($finder as $file) {
                $className = $module->getNamespace() . '\\FieldType\\' .
                    implode(DIRECTORY_SEPARATOR, explode('\\', $file->getRelativePath())) .
                    '\\' .
                    str_replace('.php', '', $file->getBasename());
                $className = preg_replace('/\\\\+/', '\\', $className);
                if (class_exists($className)) {
                    $classImplements = class_implements($className);
                    if (in_array(FieldTypeInterface::class, $classImplements)) {
                        $fieldType = new $className();
                        $fieldTypes[$fieldType->id()] = $className;
                        $container->set('field_type.' . $fieldType->id(), $fieldType);
                    }
                }
            }
        } catch (DirectoryNotFoundException $e) {
        }

        $container->setParameter('field_types', $fieldTypes);
    }
}
