<?php

namespace Puzzle\component\Controller\Api;

use Puzzle\Component\ComponentDiscovery;
use Puzzle\Component\Renderer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ComponentController
{
    public function __construct(
        protected ComponentDiscovery $componentDiscovery,
        protected Renderer $renderer
    ) {
    }

    public function index(): JsonResponse
    {
        $components = $this->componentDiscovery->getComponents();
        return new JsonResponse(array_map(function ($component) {
            return $component->toArray();
        }, $components));
    }

    public function output(string $id): JsonResponse
    {
        $component = $this->componentDiscovery->get($id);
        return new JsonResponse([
            'component' => $component->toArray(),
            'html' => $this->renderer->render($component, true)
        ]);
    }

    public function update(
        string $id,
        Request $request
    ): JsonResponse {
        $component = $this->componentDiscovery->get($id);
        $payload = $request->toArray();
        $component->updateFields($payload['fields']);
        return new JsonResponse([
            'component' => $component->toArray(),
            'html' => $this->renderer->render($component)
        ]);
    }
}
