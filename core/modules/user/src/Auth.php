<?php

namespace Puzzle\user;

use Puzzle\user\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;

class Auth
{
    protected ?User $currentUser = null;

    public function __construct(protected Session $session)
    {
    }

    public function authenticate(string $email, string $password): bool
    {
        $user = User::where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            session_regenerate_id(true);
            $this->session->set('user_id', $user->id);
            $this->currentUser = $user;
            return true;
        }

        return false;
    }

    public function getCurrentUser(): ?User
    {
        if (!$this->currentUser && $this->session->has('user_id')) {
            $this->currentUser = User::find($this->session->get('user_id'));
        }
        return $this->currentUser;
    }

    public function check(): bool
    {
        return $this->getCurrentUser() instanceof User;
    }

    public function logout(): void
    {
        $this->session->invalidate();
    }
}
