<template>
    <!-- eslint-disable vue/no-v-html -->
    <div
        ref="componentBox"
        class="relative min-h-full flex flex-col cursor-default"
        @mouseenter.stop="pageBuilder.setComponentHover(component)"
        @mouseleave.stop="pageBuilder.setComponentHover(null)"
        @click.prevent="setCurrentTarget"
        @contextmenu.stop.prevent="onRightClick"
    >
        <template v-if="componentType.settings.container">
            <slot/>
        </template>
        <div
            v-else
            ref="html"
            v-html="component.rendered_html"
        />

        <div
            v-if="isCurrentHover && !componentType.settings.container"
            class="absolute -inset-0.5 border pointer-events-none"
            :class="{'border-purple-500':!placeholder,'border-indigo-400 border-dashed':placeholder}"
        />
        <div
            v-if="menuVisible"
            class="fixed bg-indigo-500 text-white border-indigo-700 rounded shadow-lg min-w-[160px] p-1 z-50"
            :style="{ top: `${menuPosition.y}px`, left: `${menuPosition.x}px` }"
        >
            <ul class="flex flex-col gap-2">
                <li
                    class="hover:bg-indigo-400 px-2 py-1 text-sm cursor-pointer"
                    @click="handleAction('edit')"
                >
                    Edit
                </li>
                <li
                    class="hover:bg-indigo-400 px-2 py-1 text-sm cursor-pointer"
                    @click="handleAction('delete')"
                >
                    Delete
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup lang="ts">
import type {Component} from '@modules/page_builder/assets/js/types/page-builder.ts';
import {computed, createApp, onBeforeUnmount, onMounted, ref, useTemplateRef, watch} from 'vue';
import {useElementHover} from '@vueuse/core';
import Item from '@modules/page_builder/assets/js/components/Item.vue';
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';
import {useHoverStore} from '@modules/page_builder/assets/js/stores/hover';

const menuVisible = ref(false);
const menuPosition = ref({x: 0, y: 0});

// @TODO: Move to a store to keep only 1 open contextual menu open.
function onRightClick(event: MouseEvent) {
    event.preventDefault();
    menuPosition.value = {x: event.clientX, y: event.clientY};
    menuVisible.value = true;
}

function closeMenu() {
    menuVisible.value = false;
}

function handleAction(action: string) {
    if (action === 'edit') {
        pageBuilder.openSettings(props.component);
    }
    if (action === 'delete') {
        pageBuilder.removeComponent(props.component);
    }
    closeMenu();
}

document.addEventListener('click', () => {
    if (menuVisible.value) {
        closeMenu();
    }
});

const props = withDefaults(defineProps<{
    component: Component,
    componentCount: number,
}>(), {});
const pageBuilder = usePageBuilderStore();
const hover = useHoverStore();

const componentType = computed(() => pageBuilder.getComponentType(props.component.component_type));
const componentBox = ref();
const placeholder = computed(() => componentType.value.settings.placeholder);
const isHovering = useElementHover(componentBox);

const isCurrentHover = computed(() => hover.currentHover === props.component.id);

watch(isHovering, (hovered) => {
    if (componentType.value.settings.container) {
        return;
    }

    if (hovered) {
        hover.pushHover(props.component.id);
    } else {
        hover.popHover(props.component.id);
    }
});


const instances = new Map<HTMLElement, any>();

function mountComponent(el: HTMLElement, newComponent: any) {
    const app = createApp(Item, {
        componentUuid: newComponent.id,
        position: newComponent.position,
        componentCount: newComponent.children.length,
        cssClass: el.dataset.class ?? null
    });

    app.mount(el);
    instances.set(el, app);
}

onBeforeUnmount(() => {
    instances.forEach((app, el) => {
        app.unmount();
        instances.delete(el);
    });
});

const setCurrentTarget = (): void => {
    if (componentType.value.settings.container) {
        pageBuilder.setTarget(props.component, props.component.position);
    }
};

const html = useTemplateRef('html');
const renderChildren = () => {
    if (html.value) {
        let i = 0;
        for (let key in componentType.value.settings?.positions) {
            const childrenElements = html.value.querySelectorAll(`component-placeholder[data-uuid="${props.component.id}"][data-position="${key}"]`);
            childrenElements.forEach(async (el) => {
                if (el instanceof HTMLElement) {
                    const position = el.dataset.position ?? null;
                    let newComponent = props.component.children.find((child: Component) => child.position === key);
                    if (typeof newComponent === 'undefined') {
                        newComponent = await pageBuilder.createComponent(<string>el.dataset.component, {
                            component: props.component,
                            position
                        }, (++i));
                    }
                    newComponent.locked = (el.dataset.locked ?? 'false') === 'true';
                    mountComponent(el, newComponent);
                }
            });
        }

    }
};

onMounted(renderChildren);
</script>
