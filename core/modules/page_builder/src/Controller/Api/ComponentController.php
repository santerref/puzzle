<?php

namespace Puzzle\page_builder\Controller\Api;

use Illuminate\Support\Str;
use Puzzle\Core\Component\ComponentDiscovery;
use Puzzle\Core\Component\Renderer;
use Puzzle\page\Entity\Page;
use Puzzle\page_builder\ComponentFactory;
use Puzzle\page_builder\Entity\Component;
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

    public function render(string $id): JsonResponse
    {
        $componentType = $this->componentDiscovery->get($id);

        $component = $this->componentFactory->create($componentType);
        $component->setAttribute('rendered_html', $this->renderer->render($componentType, $component, [
            'page_builder' => true
        ]));

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
        foreach ($components as $component) {
            if ($component['parent'] && !isset($idMapping[$component['parent']])) {
                break;
            }

            $id = $component['id'];
            $savedComponent = $page->components()->updateOrCreate(
                ['id' => $id],
                array_diff_key($component, array_flip(['original', 'children']))
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
        $componentType = $this->componentDiscovery->get($id);
        $payload = $request->toArray();
        $formValues = $payload['form_values'];
        $uuid = $payload['uuid'] ?? Str::uuid();

        $component = new Component([
            'id' => $uuid,
            'form_values' => $formValues,
            'component_type' => $componentType->getType(),
        ]);

        return new JsonResponse([
            'form_values' => $formValues,
            'rendered_html' => $this->renderer->render($componentType, $component, [
                'page_builder' => true,
                'uuid' => $uuid
            ])
        ]);
    }

}
