import {defineStore} from 'pinia';
import {ref} from 'vue';
import {Component} from '@modules/page_builder/assets/js/types/page-builder';
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';
import {useHoverStore} from '@modules/page_builder/assets/js/stores/hover';

export const useContextualStore = defineStore('contextual', () => {
    const menuVisible = ref(false);
    const menuPosition = ref({x: 0, y: 0});
    const currentComponent = ref<Component | null>(null);
    const pageBuilder = usePageBuilderStore();
    const hover = useHoverStore();

    const openMenu = function (event: MouseEvent, component: Component) {
        event.preventDefault();

        const componentType = pageBuilder.getComponentType(component.component_type);
        if (componentType.settings.hidden) {
            if (component.parent) {
                component = <Component>pageBuilder.findComponentByUUID(pageBuilder.components, component.parent);
            } else {
                return;
            }
        }

        hover.lock();

        menuPosition.value = {x: event.clientX, y: event.clientY};
        menuVisible.value = true;

        currentComponent.value = component;
    };

    const closeMenu = function () {
        menuVisible.value = false;
        currentComponent.value = null;
        hover.unlock();
    };

    return {
        openMenu,
        menuVisible,
        menuPosition,
        currentComponent,
        closeMenu,
    };
});
