<template>
    <!-- eslint-disable vue/no-v-html -->
    <div
        ref="componentBox"
        class="relative"
        :class="{
            'outline-stone-800':!placeholder && showToolbar,
            'outline-blue-800':placeholder && showToolbar,'outline-2 z-10':showToolbar,
            'cursor-pointer':componentType.container
        }"
        @mouseenter.stop="pageBuilder.setComponentHover(component)"
        @mouseleave.stop="pageBuilder.setComponentHover(null)"
        @click.prevent="setCurrentTarget"
    >
        <template v-if="component.children.length && !componentType.placeholder">
            <slot/>
        </template>
        <template v-else>
            <div
                v-if="componentType.container && component.children.length === 0"
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
            v-if="showToolbar && !componentType.container"
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
import {Component} from '@modules/page_builder/assets/js/types/page-builder';
import {computed, h, onMounted, ref, render, useTemplateRef, watch} from 'vue';
import {useElementHover} from '@vueuse/core';
import Item from '@modules/page_builder/assets/js/components/Item.vue';
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';

const props = defineProps<{
    component: Component,
    componentCount: number
}>();
const pageBuilder = usePageBuilderStore();

const componentType = computed(() => pageBuilder.getComponentType(props.component.component_type));
const showToolbar = computed(() => pageBuilder.componentHover?.id === props.component.id);
const componentBox = ref();
const hover = useElementHover(componentBox);
const placeholder = computed(() => componentType.value.placeholder);

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

const setCurrentTarget = (): void => {
    if (componentType.value.container) {
        pageBuilder.setTarget(props.component, props.component.position);
    }
};

const html = useTemplateRef('html');
const renderChildren = () => {
    if (html.value) {
        for (let key in componentType.value.settings?.positions) {
            const childrenElements = html.value.querySelectorAll(`component-placeholder[data-uuid="${props.component.id}"][data-position="${key}"]`);
            childrenElements.forEach(async (el) => {
                if (el instanceof HTMLElement) {
                    const position = el.dataset.position ?? null;
                    let newComponent;
                    if (props.component.is_new) {
                        newComponent = await pageBuilder.createComponent(<string>el.dataset.component, {
                            component: props.component,
                            position
                        });
                    } else {
                        newComponent = props.component.children.find((child: Component) => child.position === key);
                        if (typeof newComponent === 'undefined') {
                            newComponent = await pageBuilder.createComponent(<string>el.dataset.component, {
                                component: props.component,
                                position
                            });
                        }
                    }
                    if (typeof newComponent !== 'undefined') {
                        newComponent.locked = (el.dataset.locked ?? 'false') === 'true';

                        render(h(Item, {
                            componentUuid: newComponent.id,
                            key: newComponent.id + '_' + el.dataset.position,
                            position: newComponent.position,
                            componentCount: newComponent.children.length
                        }), el);
                    }
                }
            });
        }
    }
};

onMounted(renderChildren);
</script>
