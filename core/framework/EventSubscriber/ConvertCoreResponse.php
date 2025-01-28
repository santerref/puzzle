<?php

namespace Puzzle\EventSubscriber;

use Puzzle\Event\ResponsePrepareEvent;
use Puzzle\Http\InternalRedirectResponse;
use Puzzle\Http\TwigTemplateResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ConvertCoreResponse implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ResponsePrepareEvent::NAME => 'onResponsePrepare'
        ];
    }

    public function onResponsePrepare(ResponsePrepareEvent $event)
    {
        $response = $event->getResponse();
        $twig = $event->getContainer()->get('twig');

        if ($response instanceof TwigTemplateResponse) {
            $content = $twig->render($response->getTemplate(), $response->getArgs());
            $newResponse = new Response($content);
            $event->setResponse($newResponse);
        } elseif ($response instanceof InternalRedirectResponse) {
            $url = $event->getContainer()->get('router.url_generator')->generate(
                $response->getRoute(),
                $response->getParameters()
            );
            $newResponse = new RedirectResponse($url, $response->getStatus());
            $event->setResponse($newResponse);
        }
    }
}
