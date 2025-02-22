<?php

namespace Puzzle\page_builder\EventSubscriber;

use GuzzleHttp\Client;
use Puzzle\Event\ComponentPreRender;
use Puzzle\Puzzle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GenerateDefaultImage implements EventSubscriberInterface
{
    public function __construct(protected Client $httpClient)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            ComponentPreRender::NAME => 'onComponentPreRender'
        ];
    }

    public function onComponentPreRender(ComponentPreRender $event)
    {
        $componentType = $event->getComponentType();
        if ($componentType->getType() != 'image' || !Puzzle::config()->get('unsplash.enabled', false)) {
            return;
        }

        //@TODO: Refactor, this is not possible now.
        $formValues = $event->getComponent()->getAttribute('form_values');
        if (empty($formValues['image'])) {
            $formValues['image'] = $this->getUnsplashRandomImage();
        }
        $event->getComponent()->setAttribute('form_values', $formValues);
    }

    protected function getUnsplashRandomImage(): string
    {
        $response = $this->httpClient->get('https://api.unsplash.com/photos/random', [
            'query' => [
                'orientation' => 'landscape'
            ],
            'headers' => [
                'Authorization' => 'Client-ID ' . Puzzle::config()->get('unsplash.access_key')
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['urls']['regular'];
    }
}
