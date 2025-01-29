<?php

namespace Puzzle\component\EventSubscriber;

use GuzzleHttp\Client;
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
        $info = $component->getInfo();
        $info['fields']['image']['default_value'] = $this->getUnsplashRandomImage();
        $component->setInfo($info);
    }

    protected function getUnsplashRandomImage(): string
    {
        $response = $this->httpClient->get('https://api.unsplash.com/photos/random', [
            'query' => [
                'orientation' => 'landscape'
            ],
            'headers' => [
                'Authorization' => '',
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['urls']['regular'];
    }
}
