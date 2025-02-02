<?php

namespace Puzzle\user\Controller;

use Puzzle\Http\ResponseFactory;
use Puzzle\user\Auth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    public function __construct(protected ResponseFactory $responseFactory, protected Auth $auth)
    {
    }

    public function login(): Response
    {
        return $this->responseFactory->createTwigTemplateResponse('@module_user/login.html.twig');
    }

    public function authenticate(Request $request): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        if ($this->auth->authenticate($email, $password)) {
            return $this->responseFactory->createInternalRedirectResponse('page.admin');
        }

        return $this->responseFactory->createInternalRedirectResponse('user.login');
    }

    public function logout(): Response
    {
        $this->auth->logout();
        return $this->responseFactory->createInternalRedirectResponse('core.welcome');
    }
}
