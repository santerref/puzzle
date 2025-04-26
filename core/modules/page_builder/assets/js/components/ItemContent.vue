<template>
    <!-- eslint-disable vue/no-v-html -->
    <div
        ref="componentBox"
        class="relative"
        :class="{
            'outline-stone-800':!placeholder && showToolbar,
            'outline-blue-800':placeholder && showToolbar,'outline-2 z-10':showToolbar,
            'cursor-pointer':componentType.settings.container
        }"
        @mouseenter.stop="pageBuilder.setComponentHover(component)"
        @mouseleave.stop="pageBuilder.setComponentHover(null)"
        @click.prevent="setCurrentTarget"
    >
        <template v-if="component.children.length && !componentType.settings.placeholder">
            <slot/>
        </template>
        <template v-else>
            <div
                v-if="componentType.settings.container && component.children.length === 0"
                class="p-5 flex justify-center"
            >
                <p class="text-center px-6 py-4 rounded text-stone-600 font-medium m-auto d-block bg-stone-100">
                    Click here to add components to the container
                </p>
            </div>
            <div
                v-else
                ref="html"
                v-html="component.rendered_html"
            />
        </template>

        <div
            v-if="showToolbar && !componentType.settings.container"
            class="text-stone-100 right-0 flex outline-2 gap-4 py-2 absolute rounded-bl-md top-0 px-2 shadow-lg"
            :class="{' bg-stone-800 outline-stone-800':!placeholder,' bg-blue-800 outline-blue-800':placeholder}"
        >
            <i
                v-if="componentCount > 1"
                class="pi handle text-stone-100 pi-arrows-alt hover:cursor-grab"
            />
            <i
                class="pi pi-cog hover:cursor-pointer"
                @click.prevent="pageBuilder.openSettings(component)"
            />
            <i
                v-if="!component.locked"
                class="pi text-stone-100 pi-trash hover:cursor-pointer"
                @click.prevent="pageBuilder.removeComponent(component)"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import type {Component} from '@modules/page_builder/assets/js/types/page-builder.ts';
import {computed, createApp, onBeforeUnmount, onMounted, ref, useTemplateRef, watch} from 'vue';
import {useElementHover} from '@vueuse/core';
import Item from '@modules/page_builder/assets/js/components/Item.vue';
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';

const props = withDefaults(defineProps<{
    component: Component,
    componentCount: number,
}>(), {});
const pageBuilder = usePageBuilderStore();

const componentType = computed(() => pageBuilder.getComponentType(props.component.component_type));
const showToolbar = computed(() => pageBuilder.componentHover?.id === props.component.id);
const componentBox = ref();
const hover = useElementHover(componentBox);
const placeholder = computed(() => componentType.value.settings.placeholder);

watch(hover, (newValue) => {
    if (!newValue) {
        pageBuilder.setComponentHover(null);
    }
});

watch(() => pageBuilder.componentHover, (hoverComponent: Component | null) => {
    if (hover.value && hoverComponent === null) {
        pageBuilder.setComponentHover(props.component);
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
