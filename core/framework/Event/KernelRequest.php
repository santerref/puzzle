<?php

namespace Puzzle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class KernelRequest extends Event
{
    public const NAME = 'kernel.request';

    public function __construct(private Request $request)
    {
    }

    public function getRequest()
    {
        return $this->request;
    }
}
