<template>
    <!-- eslint-disable vue/no-v-html -->
    <div>
        <div
            class="relative"
            :class="{'outline-stone-800':!container && showToolbar,'outline-blue-800':container && showToolbar,'outline-2 cursor-pointer z-10':showToolbar}"
            ref="componentBox"
            @mouseenter.stop="pageBuilder.setComponentHover(component)"
            @mouseleave.stop="pageBuilder.setComponentHover(null)"
            @click.stop="pageBuilder.setTarget(component, position)"
        >
            <template v-if="component.children.length && !componentType.placeholder">
                <nested-component v-model="component.children"/>
            </template>
            <template v-else>
                <div
                    ref="html"
                    v-html="component.rendered_html"
                />
            </template>

            <div
                v-if="showToolbar"
                class="text-stone-100 flex gap-4 py-3 absolute top-0 px-4 shadow-lg"
                :class="{'-right-0.5 bg-stone-800 rounded-bl-md':!container,'-left-0.5 bg-blue-800 rounded-br-md':container}"
            >
                <i class="pi handle text-stone-100 pi-arrows-alt hover:cursor-grab"/>
                <!--                <i
                                    class="pi pi-pencil hover:cursor-pointer"
                                    @click.prevent="components.editComponent(component)"
                                />-->
                <!--                <i
                                    v-if="!component.live.locked"
                                    class="pi text-stone-100 pi-trash hover:cursor-pointer"
                                    @click.prevent="remove(component)"
                                />-->
                <!--                <i
                                    class="pi text-stone-100 pi-sync hover:cursor-pointer"
                                    @click.prevent="components.rerender(component)"
                                />
                                <i
                                    v-if="isDirty"
                                    class="pi text-stone-100 pi-undo hover:cursor-pointer"
                                    @click.prevent="undo(component)"
                                />-->
                <!--                <i
                                    v-if="component.live.weight > 1 && component.children.length > 1"
                                    class="pi text-stone-100 pi-angle-up hover:cursor-pointer"
                                    @click.prevent="components.moveUp(component)"
                                />
                                <i
                                    v-if="component.live.weight < components.pageBuilderItems.length && component.children.length > 1"
                                    class="pi text-stone-100 pi-angle-down hover:cursor-pointer"
                                    @click.prevent="components.moveDown(component)"
                                />-->
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {Component} from "@modules/page_builder/assets/js/types/page-builder";
import {computed, h, onMounted, ref, render, useTemplateRef, watch} from "vue";
import {usePageBuilderStore} from "@modules/page_builder/assets/js/stores/page-builder";
import Item from "@modules/page_builder/assets/js/components/Item.vue";
import {useElementHover} from "@vueuse/core";
import NestedComponent from "@modules/page_builder/assets/js/components/NestedComponent.vue";

const pageBuilder = usePageBuilderStore()
const props = withDefaults(defineProps<{
    componentUuid: string,
    placeholder?: boolean,
    position?: string | null
}>(), {
    placeholder: false,
    position: null
})

const component = computed<Component>(() => {
    function findComponentByUUID(components: Component[], uuid: string) {
        for (const component of components) {
            if (component.id === props.componentUuid) {
                return component;
            }

            const foundInChildren = findComponentByUUID(component.children, uuid);
            if (foundInChildren) {
                return foundInChildren;
            }
        }

        return null;
    }

    return findComponentByUUID(pageBuilder.components, props.componentUuid);
})
const componentType = computed(() => pageBuilder.getComponentType(component.value.component_type));
const showToolbar = computed(() => pageBuilder.componentHover?.id === component.value.id)
const container = computed(() => pageBuilder.getComponentType(component.value.component_type).container)
const componentBox = ref()
const hover = useElementHover(componentBox)

watch(hover, (newValue) => {
    if (!newValue) {
        pageBuilder.setComponentHover(null)
    }
})

watch(() => pageBuilder.componentHover, (hoverComponent: Component | null) => {
    if (hover.value && hoverComponent === null) {
        pageBuilder.setComponentHover(component.value)
    }
})

const html = useTemplateRef('html')
const renderChildren = () => {
    if (html.value) {
        for (let key in componentType.value.settings?.positions) {
            const position = componentType.value.settings?.positions[key];
            const childrenElements = html.value.querySelectorAll(`component-placeholder[data-uuid="${component.value.id}"][data-position="${key}"]`)
            childrenElements.forEach(async (el) => {
                const position = el.dataset.position ?? null
                let newComponent;
                if (component.value.is_new) {
                    newComponent = await pageBuilder.createComponent(el.dataset.component, {
                        component: component.value,
                        position
                    });
                } else {
                    newComponent = <Component>component.value.children.find((child: Component) => child.position === key)
                }
                render(
                    h(Item, {
                        componentUuid: newComponent.id,
                        key: newComponent.id + '_' + el.dataset.position,
                        position
                    }),
                    el
                )
            })
        }
    }
}

onMounted(renderChildren)
</script>
