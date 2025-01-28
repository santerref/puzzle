<?php

namespace Puzzle\Event;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class ResponsePrepareEvent extends Event
{
    public const NAME = 'response.prepare';

    public function __construct(
        protected mixed $response,
        protected ContainerBuilder $container
    ) {
    }

    public function getResponse(): mixed
    {
        return $this->response;
    }

    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
