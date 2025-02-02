<?php

namespace Puzzle\user\Middleware;

use Puzzle\Http\Middleware\MiddlewareInterface;
use Puzzle\Http\ResponseFactory;
use Puzzle\user\Auth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AnonymousMiddleware implements MiddlewareInterface
{
    public function __construct(protected Auth $auth, protected ResponseFactory $responseFactory)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        if ($this->auth->check()) {
            //@TODO: Set a setting for the redirect when already logged in.
            return $this->responseFactory->createInternalRedirectResponse('page.admin');
        }

        return $next($request);
    }
}
