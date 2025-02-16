<?php

namespace Puzzle\Http\Controller;

use Puzzle\Storage\Entity\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class EntityResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $class = $argument->getType();
        if (class_exists($class)) {
            $classParents = class_parents($argument->getType());
            foreach ($classParents as $classParent) {
                if ($classParent == Entity::class) {
                    $entity = call_user_func_array(
                        [$class, 'findOrFail'],
                        [$request->attributes->get($argument->getName())]
                    );

                    return [$entity];
                }
            }
        }

        return [];
    }
}
