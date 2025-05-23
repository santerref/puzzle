<?php

namespace Puzzle\admin\EventSubscriber;

use Puzzle\admin\Exceptions\InvalideCsrfTokenException;
use Puzzle\Event\KernelRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class ValidateCsrfToken implements EventSubscriberInterface
{
    public function __construct(private readonly CsrfTokenManager $csrfTokenManager)
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

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $submittedToken = $request->request->get('_csrf_token');

            if (empty($submittedToken)) {
                $submittedToken = $request->headers->get('X-Puzzle-Csrf-Token');
            }

            if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('form_csrf', $submittedToken))) {
                throw new InvalideCsrfTokenException();
            }
        }
    }
}
