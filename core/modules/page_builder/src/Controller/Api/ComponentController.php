<?php

namespace Puzzle\page_builder\Controller\Api;

use Illuminate\Support\Str;
use Puzzle\Core\Component\ComponentDiscovery;
use Puzzle\Core\Component\Renderer;
use Puzzle\page\Entity\Page;
use Puzzle\page_builder\ComponentFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ComponentController
{
    public function __construct(
        protected ComponentDiscovery $componentDiscovery,
        protected Renderer $renderer,
        protected ComponentFactory $componentFactory
    ) {
    }

    public function index(): JsonResponse
    {
        $components = $this->componentDiscovery->getComponents();
        return new JsonResponse(array_values($components));
    }

    public function render(string $id, Request $request): JsonResponse
    {
        $componentType = $this->componentDiscovery->get($id);
        $payload = $request->isMethod('PUT') ? $request->toArray() : [];
        $componentFields = $payload['component_fields'] ?? [];
        $uuid = $payload['uuid'] ?? Str::uuid();
        $component = $this->componentFactory->create($uuid, $componentType, $componentFields);

        return new JsonResponse($component);
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

        return new JsonResponse($page->load([
            'components' => function ($query) {
                $query->whereNull('parent')->orderBy('weight');
            }
        ]));
    }

    //@TODO: Refactor and move right place.
    protected function saveComponents(Page $page, array $components, array &$idMapping, array &$savedComponents)
    {
        foreach ($components as $componentWeight => $component) {
            if ($component['parent'] && !isset($idMapping[$component['parent']])) {
                break;
            }

            $id = $component['id'];
            $savedComponent = $page->components()->updateOrCreate(
                ['id' => $id],
                array_diff_key($component, array_flip(['original', 'children'])) + ['weight' => $componentWeight]
            );
            foreach ($component['component_fields'] as $componentFieldWeight => $componentField) {
                $savedComponent->componentFields()->updateOrCreate(
                    ['id' => $componentField['id']],
                    $componentField + ['weight' => $componentFieldWeight]
                );
            }
            $savedComponents[] = $savedComponent->id;
            $idMapping[$savedComponent->id] = $savedComponent->id;
        }

        if (count($savedComponents) !== count($components)) {
            $this->saveComponents($page, $components, $idMapping, $savedComponents);
        }
    }

}
