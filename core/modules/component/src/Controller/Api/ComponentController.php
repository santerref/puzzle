<?php

namespace Puzzle\component\Controller\Api;

use Puzzle\Component\ComponentDiscovery;
use Puzzle\Component\Renderer;
use Puzzle\page\Entity\Page;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function create(string $id): JsonResponse
    {
        $component = $this->componentDiscovery->get($id);
        return new JsonResponse([
            'component_type' => $id,
            'form_values' => $component->getDefaultValues(),
            'rendered_html' => $this->renderer->render($component, true)
        ]);
    }

    public function save(Page $page, Request $request): JsonResponse
    {
        $components = $request->getPayload()->all();
        $updatedComponents = [];
        foreach ($components as $component) {
            if (preg_match('/^temporary:/', $component['id'])) {
                unset($component['id']);
                $updatedComponent = $page->components()->create($component);
                $updatedComponents[] = $updatedComponent->id;
            } else {
                $page->components()->where('id', $component['id'])->update($component);
                $updatedComponents[] = $component['id'];
            }
        }
        $page->components()->whereNotIn('id', $updatedComponents)->delete();
        $page->refresh();

        return new JsonResponse($page);
    }

    public function refresh(
        string $id,
        Request $request
    ): JsonResponse {
        $component = $this->componentDiscovery->get($id);
        $payload = $request->toArray();
        $component->updateFields($payload['form_values']);
        return new JsonResponse([
            'component_type' => $id,
            'form_values' => $payload['form_values'],
            'rendered_html' => $this->renderer->render($component)
        ]);
    }
}
