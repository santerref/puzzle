<?php

namespace Puzzle\Controller\Api;

use Puzzle\Component\ComponentDiscovery;
use Puzzle\Component\Renderer;
use Puzzle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

class ComponentController extends BaseController
{
    public function index(ComponentDiscovery $componentDiscovery)
    {
        $components = $componentDiscovery->getComponents();
        return $this->json(array_map(function ($component) {
            return $component->toArray();
        }, $components));
    }

    public function output(string $id, ComponentDiscovery $componentDiscovery, Renderer $renderer)
    {
        $component = $componentDiscovery->get($id);
        return $this->json([
            'component' => $component->toArray(),
            'html' => $renderer->render($component, true)
        ]);
    }

    public function update(string $id, Request $request, ComponentDiscovery $componentDiscovery, Renderer $renderer)
    {
        $component = $componentDiscovery->get($id);
        $payload = $request->toArray();
        $component->updateFields($payload['fields']);
        return $this->json([
            'component' => $component->toArray(),
            'html' => $renderer->render($component)
        ]);
    }
}
