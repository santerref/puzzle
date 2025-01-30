<?php

namespace Puzzle\component\EventSubscriber;

use GuzzleHttp\Client;
use Puzzle\Config;
use Puzzle\Event\ComponentPreRender;
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
        $component = $event->getComponent();
        $formValues = $event->getFormValues();
        $formValues['image'] = $this->getUnsplashRandomImage();
        $event->setValues($formValues);
    }

    protected function getUnsplashRandomImage(): string
    {
        $response = $this->httpClient->get('https://api.unsplash.com/photos/random', [
            'query' => [
                'orientation' => 'landscape'
            ],
            'headers' => [
                'Authorization' => 'Client-ID ' . Config::get('unsplash.access_key')
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['urls']['regular'];
    }
}
