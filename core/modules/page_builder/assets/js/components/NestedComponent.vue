<template>
    <draggable
        v-model="draggableComponents"
        :item-key="itemKey"
        :group="group"
        :class="{'min-h-[200px]':empty}"
        @change="updateParent"
        @start="hover.lock"
        @end="hover.unlock"
    >
        <template #item="{element}">
            <div class="relative">
                <item
                    :id="element.id"
                    :data-uuid="element.id"
                    :component-count="draggableComponents.length"
                    :component-uuid="element.id"
                />
            </div>
        </template>
        <template #header>
            <div
                v-if="empty"
                class="absolute -inset-1 border-2 bg-indigo-50 opacity-50 border-dashed border-indigo-300"
            />
        </template>
    </draggable>
</template>

<script setup lang="ts">
import type {Component} from '@modules/page_builder/assets/js/types/page-builder';
import {computed} from 'vue';
import Item from '@modules/page_builder/assets/js/components/Item.vue';
import hash from 'object-hash';
import draggable from 'vuedraggable/src/vuedraggable';
import {useHoverStore} from '@modules/page_builder/assets/js/stores/hover';

const updateParent = function (event) {
    if (props.component && event.hasOwnProperty('added')) {
        if (event.added.element.parent !== props.component.id) {
            event.added.element.parent = props.component.id;
            event.added.element.position = props.component.position;
        }
    }
};

const hover = useHoverStore();
const empty = computed(() => draggableComponents.value.length === 0 && props.container);

const props = withDefaults(defineProps<{
    modelValue: Component[],
    container?: boolean,
    component?: Component | null
}>(), {
    container: false,
    component: null
});

const itemKey = function (element) {
    return getComponentHash(element);
};

const emits = defineEmits<{
    (e: 'update:modelValue', value: Component[]): void
}>();

const group = computed(() => props.container ? {name: 'container', pull: true, put: true} : {
    name: 'section',
    pull: false,
    put: false
});

const draggableComponents = computed({
    get: () => props.modelValue,
    set: (value: Component[]) => emits('update:modelValue', value)
});

const getComponentHash = (component: Component): string => {
    return hash(component.component_fields.sort((a, b) => a.weight - b.weight));
};
</script>
