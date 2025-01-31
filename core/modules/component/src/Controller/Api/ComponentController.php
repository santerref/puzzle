<?php

namespace Puzzle\component\Controller\Api;

use Illuminate\Support\Str;
use Puzzle\Component\ComponentDiscovery;
use Puzzle\Component\Renderer;
use Puzzle\page\Entity\Page;
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

    public function create(string $id): JsonResponse
    {
        $component = $this->componentDiscovery->get($id);
        $formValues = $component->getDefaultValues();
        $uuid = Str::uuid();
        return new JsonResponse([
            'id' => $uuid,
            'component_type' => $id,
            'form_values' => $formValues,
            'rendered_html' => $this->renderer->render($component, $formValues, [
                'page_builder' => true,
                'uuid' => $uuid
            ])
        ]);
    }

    public function save(Page $page, Request $request): JsonResponse
    {
        $components = $request->getPayload()->all();
        if (!empty($components)) {
            $idMapping = [];
            $savedComponents = [];
            $this->saveComponents($page, $components, $idMapping, $savedComponents);
            $page->components()->whereNotIn('id', $savedComponents)->delete();
        } else {
            $page->components()->delete();
        }

        $page->refresh();

        return new JsonResponse($page);
    }

    //@TODO: Refactor and move right place.
    protected function saveComponents(Page $page, array $components, array &$idMapping, array &$savedComponents)
    {
        foreach ($components as $component) {
            if ($component['parent'] && !isset($idMapping[$component['parent']])) {
                break;
            }

            $id = $component['id'];
            $savedComponent = $page->components()->updateOrCreate(
                ['id' => $id],
                $component
            );
            $savedComponents[] = $savedComponent->id;
            $idMapping[$savedComponent->id] = $savedComponent->id;
        }

        if (count($savedComponents) !== count($components)) {
            $this->saveComponents($page, $components, $idMapping, $savedComponents);
        }
    }

    public function refresh(
        string $id,
        Request $request
    ): JsonResponse {
        $component = $this->componentDiscovery->get($id);
        $payload = $request->toArray();
        $formValues = $payload['form_values'];
        $uuid = $payload['uuid'] ?? Str::uuid();
        return new JsonResponse([
            'id' => $uuid,
            'component_type' => $id,
            'form_values' => $formValues,
            'rendered_html' => $this->renderer->render($component, $formValues, [
                'page_builder' => true,
                'uuid' => $uuid
            ])
        ]);
    }
}
