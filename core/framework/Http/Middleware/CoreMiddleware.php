<?php

namespace Puzzle\Http\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class CoreMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected ControllerResolver $controllerResolver,
        protected ArgumentResolver $argumentResolver
    ) {
    }

    public function handle(Request $request, callable $next): Response
    {
        $controller = $this->controllerResolver->getController($request);
        $arguments = $this->argumentResolver->getArguments($request, $controller);
        return call_user_func_array($controller, $arguments);
    }
}
