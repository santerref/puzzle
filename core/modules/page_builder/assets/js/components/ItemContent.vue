<template>
    <!-- eslint-disable vue/no-v-html -->
    <div
        ref="componentBox"
        class="tw:relative tw:min-h-full tw:flex tw:flex-col tw:cursor-default"
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
            class="tw:absolute tw:-inset-0.5 tw:z-30 tw:border tw:pointer-events-none tw:rounded"
            :class="{'tw:border-indigo-500':!placeholder,'tw:border-indigo-500 tw:border-dashed':placeholder}"
        >
            <div
                v-if="componentType"
                class="tw:absolute tw:bg-indigo-500 tw:text-white tw:lowercase tw:font-bold tw:-translate-y-full tw:left-2 tw:rounded-tl tw:rounded-tr tw:text-xs tw:px-1 tw:py-0.5"
            >
                {{ componentType.name }}
            </div>
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
import {useContextualStore} from '@modules/page_builder/assets/js/stores/contextual';
import {sortBy} from 'lodash';

const contextual = useContextualStore();

// @TODO: Move to a store to keep only 1 open contextual menu open.
function onRightClick(event: MouseEvent) {
    contextual.openMenu(event, props.component);
}

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
const renderChildren = async () => {
    if (html.value) {
        let i = 0;
        const newComponents: { el: HTMLElement, position: string | null, add: boolean, component: Component }[] = [];
        const tasks: Promise<void>[] = [];
        for (let key in componentType.value.settings?.positions) {
            const childrenElements = html.value.querySelectorAll(`component-placeholder[data-uuid="${props.component.id}"][data-position="${key}"]`);
            childrenElements.forEach((el) => {
                if (el instanceof HTMLElement) {
                    const task = (async () => {
                        const position = el.dataset.position ?? null;
                        const weight = el.dataset.weight !== undefined ? parseInt(el.dataset.weight) : (++i);
                        let newComponent = props.component.children.find((child: Component) => child.position === key);
                        let add = true;
                        if (typeof newComponent === 'undefined') {
                            newComponent = await pageBuilder.createComponent(<string>el.dataset.component, {
                                component: props.component,
                                position
                            }, weight, false);
                        } else {
                            newComponent.weight = weight;
                            add = false;
                        }
                        newComponent.locked = (el.dataset.locked ?? 'false') === 'true';
                        newComponents.push({
                            el,
                            position,
                            add,
                            component: newComponent
                        });
                    })();
                    tasks.push(task);
                }
            });
        }

        await Promise.all(tasks);
        const sortedNewComponents = sortBy(newComponents, 'component.weight');

        sortedNewComponents.forEach((item) => {
            if (item.add) {
                pageBuilder.addComponent(item.component, {
                    component: props.component,
                    position: item.position
                });
            }
            mountComponent(item.el, item.component);
        });
    }
};

onMounted(renderChildren);
</script>
