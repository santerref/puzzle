import {defineStore} from 'pinia';
import {computed, ref} from 'vue';
import type {Component, ComponentType, Page, Target} from '@modules/page_builder/assets/js/types/page-builder';
import cloneDeep from 'clone-deep';
import deepEqual from 'deep-equal';

export const usePageBuilderStore = defineStore('pageBuilder', () => {
    const components = ref<Component[]>([]);
    const componentTypes = ref<ComponentType[]>([]);
    const currentPage = ref<Page | null>(null);
    const currentTarget = ref<Target | null>(null);
    const currentPageUuid = ref<string>(window.pageUuid);
    const isLoading = ref<boolean>(true);
    const componentHover = ref<Component | null>(null);
    const currentComponentSettings = ref<Component | null>(null);
    const mountedComponents = ref<Record<string, boolean>>({});

    const saveRequired = computed(() => !deepEqual(page.value.components, components.value));
    const page = computed<Page>(() => <Page>currentPage.value);
    const loading = computed<boolean>(() => isLoading.value);
    const isRoot = computed<boolean>(() => currentTarget.value === null);

    const flatComponents = computed(() => {
        const assignWeight = (components: Component[]): Component[] => {
            return components.flatMap((component, index) => {
                const weightedComponent = {
                    ...component,
                    weight: index,
                };

                return [weightedComponent, ...assignWeight(component.children)];
            });
        };

        return assignWeight(components.value).filter(component => isMounted(component.id));
    });

    const availableComponentTypes = computed<ComponentType[]>(() => {
        if (isRoot.value) {
            return componentTypes.value.filter((componentType: ComponentType) => componentType.settings.root);
        }
        return componentTypes.value.filter((componentType: ComponentType) => {
            return !componentType.settings.root && !componentType.settings.hidden;
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

    async function loadComponentTypes(): Promise<ComponentType[]> {
        const response = await fetch('/api/components');
        return (await response.json()) as ComponentType[];
    }

    async function loadPage(): Promise<Page> {
        const response = await fetch(`/api/pages/${currentPageUuid.value}`);
        return (await response.json()) as Page;
    }

    async function createComponent(componentTypeId: string, target?: Target, weight?: number): Promise<Component> {
        const componentType = getComponentType(componentTypeId);
        const response = await fetch(`/api/components/${componentType.id}/render`, {
            method: 'POST'
        });

        const component = await response.json() as Component;
        component.children = [];
        component.position = null;

        if (weight) {
            component.weight = weight;
        }

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

    function setMounted(uuid: string, mounted: boolean): void {
        mountedComponents.value[uuid] = mounted;
    }

    function isMounted(uuid: string): boolean {
        return mountedComponents.value.hasOwnProperty(uuid) &&
            mountedComponents.value[uuid] === true;
    }

    function removeComponent(component: Component, children: Component[] | null = null): boolean {
        if (children === null) {
            children = components.value;
        }

        for (let i = 0; i < children.length; i++) {
            if (children[i].id === component.id) {
                children.splice(i, 1);
                return true;
            }

            if (children[i].children) {
                removeComponent(component, children[i].children);
            }
        }
        return false;
    }

    async function save(): Promise<void> {
        const response = await fetch(`/api/pages/${currentPageUuid.value}/components`, {
            method: 'PUT',
            body: JSON.stringify(flatComponents.value)
        });
        const updatedPage = await response.json();
        currentPage.value = cloneDeep(updatedPage);
        components.value = cloneDeep(updatedPage.components);
    }

    function openSettings(component: Component): void {
        currentComponentSettings.value = component;
    }

    function closeSettings(): void {
        currentComponentSettings.value = null;
    }

    function setTarget(component: Component, position?: string | null): void {
        currentTarget.value = {
            component,
            position: position
        } as Target;
    }

    function unsetTarget(): void {
        currentTarget.value = null;
    }

    function setComponentHover(component: Component | null): void {
        componentHover.value = component;
    }

    function getComponentType(id: string): ComponentType {
        return <ComponentType>componentTypes.value.find((componentType: ComponentType) => componentType.id === id);
    }

    function hasTarget(): boolean {
        return currentTarget.value !== null;
    }

    function currentTargetIs(component: Component): boolean {
        return currentTarget.value !== null &&
            currentTarget.value.component.id === component.id &&
            currentTarget.value.position === component.position;
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
        saveRequired,
        openSettings,
        closeSettings,
        currentComponentSettings,
        hasTarget,
        unsetTarget,
        currentTargetIs,
        removeComponent,
        setMounted,
        mountedComponents,
        isMounted
    };
});
