<?php

namespace Puzzle\admin\EventSubscriber;

use Puzzle\Event\KernelRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class UpdateFormRequestMethod implements EventSubscriberInterface
{
    public function __construct(private readonly UrlMatcher $router)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelRequest::NAME => 'onKernelRequest'
        ];
    }

    public function onKernelRequest(KernelRequest $event)
    {
        $request = $event->getRequest();
        if ($request->isMethod('POST') && $request->request->has('_method')) {
            $method = strtoupper($request->request->get('_method'));

            if (in_array($method, ['PUT', 'PATCH', 'DELETE'], true)) {
                $request->setMethod($method);
                $this->router->getContext()->setMethod($method);
            }
        }
    }
}
