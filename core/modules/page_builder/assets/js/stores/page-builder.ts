import {defineStore} from 'pinia'
import {computed, ref} from "vue";
import {Component, ComponentType, Page, Target} from '@modules/page_builder/assets/js/types/page-builder';
import cloneDeep from "clone-deep";
import deepEqual from "deep-equal";

export const usePageBuilderStore = defineStore('pageBuilder', () => {
    const components = ref<Component[]>([]);
    const componentTypes = ref<ComponentType[]>([]);
    const currentPage = ref<Page | null>(null);
    const currentTarget = ref<Target | null>(null);
    const currentPageUuid = ref<string>(window.pageUuid);
    const isLoading = ref<boolean>(true);
    const componentHover = ref<Component | null>(null);

    const flatComponents = computed(() => {
        const assignWeight = (components: Component[], parentWeight: number = 0): Component[] => {
            return components.flatMap((component, index) => {
                const weightedComponent = {
                    ...component,
                    weight: index,
                };

                return [weightedComponent, ...assignWeight(component.children, index)];
            });
        };

        return assignWeight(components.value);
    });
    const saveRequired = computed(() => !deepEqual(page.value.components, components.value))
    const page = computed<Page>(() => <Page>currentPage.value);
    const loading = computed<boolean>(() => isLoading.value);
    const isRoot = computed<boolean>(() => currentTarget.value === null);
    const availableComponentTypes = computed<ComponentType[]>(() => {
        if (isRoot.value) {
            return componentTypes.value.filter((componentType: ComponentType) => componentType.root);
        }
        return componentTypes.value.filter((componentType: ComponentType) => {
            return !componentType.root && !componentType.hidden;
        });
    });

    function setComponents(updatedComponents: Component[]): void {
        components.value = updatedComponents;
    }

    async function initialize(): Promise<void> {
        isLoading.value = true;
        [currentPage.value, componentTypes.value] = await Promise.all([
            loadPage(),
            loadComponentTypes()
        ]);
        components.value = cloneDeep(page.value.components);
        isLoading.value = false;
    }

    function setComponentHover(component: Component | null) {
        componentHover.value = component
    }

    function getComponentType(id: string): ComponentType {
        return <ComponentType>componentTypes.value.find((componentType: ComponentType) => componentType.id === id)
    }

    async function loadComponentTypes(): Promise<ComponentType[]> {
        const response = await fetch('/api/components');
        return (await response.json()) as ComponentType[];
    }

    async function loadPage(): Promise<Page> {
        const response = await fetch(`/api/pages/${currentPageUuid.value}`);
        return (await response.json()) as Page;
    }

    async function createComponent(componentTypeId: string, target?: Target): Promise<Component> {
        const componentType = getComponentType(componentTypeId);
        const response = await fetch(`/api/components/${componentType.id}/render`, {
            method: 'POST'
        });

        const component = await response.json() as Component;
        component.children = [];
        component.position = null;

        if (target) {
            component.position = target.position;
            component.parent = target.component.id;
            target.component.children.splice(target.component.children.length, 0, component);
        } else if (currentTarget.value) {
            component.position = currentTarget.value.position;
            component.parent = currentTarget.value.component.id;
            currentTarget.value.component.children.splice(currentTarget.value.component.children.length, 0, component);
        } else {
            component.parent = null;
            components.value.push(component);
        }

        return component;
    }

    function setTarget(component: Component, position?: string | null): void {
        currentTarget.value = {
            component,
            position: position
        } as Target;
    }

    async function save() {
        const response = await fetch(`/api/pages/${currentPageUuid.value}/components`, {
            method: 'PUT',
            body: JSON.stringify(flatComponents.value)
        })
        const updatedPage = await response.json();
        currentPage.value = cloneDeep(updatedPage);
        components.value = cloneDeep(updatedPage.components);
    }

    return {
        componentTypes,
        components,
        page,
        loading,
        save,
        currentTarget,
        setTarget,
        availableComponentTypes,
        getComponentType,
        setComponentHover,
        componentHover,
        initialize,
        setComponents,
        createComponent,
        flatComponents,
        saveRequired
    }
})


/*

    function editComponent(pageComponent: PageBuilderItem | null) {
        currentEdit.value = pageComponent
    }


    function moveUp(component: PageBuilderItem) {
        const siblings = pageBuilderItems.value.filter(obj => obj.live.parent === component.live.parent)

        const index = siblings.findIndex(obj => obj.live.id === component.live.id)
        if (index > 0) {
            const globalIndex = pageBuilderItems.value.findIndex(obj => obj.live.id === component.live.id)
            const prevSiblingGlobalIndex = pageBuilderItems.value.findIndex(obj => obj.live.id === siblings[index - 1].live.id)

            ;[pageBuilderItems.value[globalIndex], pageBuilderItems.value[prevSiblingGlobalIndex]] =
                [pageBuilderItems.value[prevSiblingGlobalIndex], pageBuilderItems.value[globalIndex]]

            ;[pageBuilderItems.value[globalIndex].live.weight, pageBuilderItems.value[prevSiblingGlobalIndex].live.weight] =
                [pageBuilderItems.value[prevSiblingGlobalIndex].live.weight, pageBuilderItems.value[globalIndex].live.weight]

            pageBuilderItems.value[globalIndex].rerender = true
            pageBuilderItems.value[prevSiblingGlobalIndex].rerender = true
        }
    }

    function moveDown(component: PageBuilderItem) {
        const siblings = pageBuilderItems.value.filter(obj => obj.live.parent === component.live.parent)

        const index = siblings.findIndex(obj => obj.live.id === component.live.id)
        if (index < siblings.length - 1) {
            const globalIndex = pageBuilderItems.value.findIndex(obj => obj.live.id === component.live.id)
            const nextSiblingGlobalIndex = pageBuilderItems.value.findIndex(obj => obj.live.id === siblings[index + 1].live.id)

            ;[pageBuilderItems.value[globalIndex], pageBuilderItems.value[nextSiblingGlobalIndex]] =
                [pageBuilderItems.value[nextSiblingGlobalIndex], pageBuilderItems.value[globalIndex]]

            ;[pageBuilderItems.value[globalIndex].live.weight, pageBuilderItems.value[nextSiblingGlobalIndex].live.weight] =
                [pageBuilderItems.value[nextSiblingGlobalIndex].live.weight, pageBuilderItems.value[globalIndex].live.weight]

            pageBuilderItems.value[globalIndex].rerender = true
            pageBuilderItems.value[nextSiblingGlobalIndex].rerender = true
        }
    }

    function remove(component: PageBuilderItem) {
        const index = pageBuilderItems.value.findIndex(obj => obj.live.id === component.live.id)
        if (index !== -1) {
            removeChildren(component.live.id)
            pageBuilderItems.value.splice(index, 1)
            if (currentPosition.value.uuid !== null) {
                const currentPositionIndex = pageBuilderItems.value.findIndex(obj => obj.live.id === currentPosition.value.uuid)
                if (currentPositionIndex === -1) {
                    //@TODO: Select nearest component, not root.
                    setCurrentComponent(null, null)
                }
            }
        }
    }

    function updateEditors(components: PageBuilderItem[]) {
        components.forEach((component, index) => {
            component.live.weight = index + 1
            return component
        })
    }

    async function save() {
        try {
            const response = await fetch(`/api/pages/${currentPageUuid.value}/components`, {
                method: 'PUT',
                body: JSON.stringify(pageBuilderItems.value.map(pageBuilderItem => pageBuilderItem.live))
            })
            const page = await response.json()
            loadPageComponents(page)
            pageBuilderItems.value = clone(originalPageBuilderItems.value)
            // eslint-disable-next-line @typescript-eslint/no-unused-vars
        } catch (e) {

        }
    }

})*/
