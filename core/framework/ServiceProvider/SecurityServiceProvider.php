<?php

namespace Puzzle\ServiceProvider;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

class SecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerSessionAndCsrf();
        $this->registerPasswordHasher();
    }

    protected function registerSessionAndCsrf(): void
    {
        $sessionDefinition = new Definition(Session::class);
        $sessionDefinition->setPublic(true);
        $sessionDefinition->addMethodCall('start');
        $this->container->setDefinition('session', $sessionDefinition);
        $this->container->setAlias(Session::class, 'session');

        $tokenStorageDefinition = new Definition(SessionTokenStorage::class);
        $tokenStorageDefinition
            ->setPublic(true)
            ->setArgument(0, new Reference(Session::class));
        $this->container->setDefinition(SessionTokenStorage::class, $tokenStorageDefinition);

        $csrfTokenManagerDefinition = new Definition(CsrfTokenManager::class);
        $csrfTokenManagerDefinition
            ->setPublic(true)
            ->setArgument(0, null)
            ->setArgument(1, new Reference(SessionTokenStorage::class));
        $this->container->setDefinition('csrf_token_manager', $csrfTokenManagerDefinition);
        $this->container->setAlias(CsrfTokenManager::class, 'csrf_token_manager');
    }

    protected function registerPasswordHasher(): void
    {
        $hasherDefinition = new Definition(NativePasswordHasher::class);
        $hasherDefinition->setPublic(true);
        $this->container->setDefinition('password_hasher', $hasherDefinition);
        $this->container->setAlias(NativePasswordHasher::class, 'password_hasher');
    }
}
