<template>
    <draggable
        v-model="draggableComponents"
        :item-key="itemKey"
        :group="group"
        :class="{'min-h-[200px]':empty}"
        @change="updateParent"
        @start="hover.lock"
        @end="endDrag"
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
            >
                <!-- eslint-disable vue/max-attributes-per-line -->
                <svg width="50" height="50" class="fill-indigo-300 absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <g>
                        <path fill="none" d="M0 0h24v24H0z"/>
                        <path fill-rule="nonzero" d="M16 13l6.964 4.062-2.973.85 2.125 3.681-1.732 1-2.125-3.68-2.223 2.15L16 13zm-2-7h2v2h5a1 1 0 0 1 1 1v4h-2v-3H10v10h4v2H9a1 1 0 0 1-1-1v-5H6v-2h2V9a1 1 0 0 1 1-1h5V6zM4 14v2H2v-2h2zm0-4v2H2v-2h2zm0-4v2H2V6h2zm0-4v2H2V2h2zm4 0v2H6V2h2zm4 0v2h-2V2h2zm4 0v2h-2V2h2z"/>
                    </g>
                </svg>
            </div>
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
import {sortBy} from 'lodash';

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

const endDrag = () => {
    hover.unlock();
    draggableComponents.value.forEach((component, index) => {
        component.weight = index;
    });
};

const group = computed(() => props.container ? {name: 'container', pull: true, put: true} : {
    name: 'section',
    pull: false,
    put: false
});

const draggableComponents = computed({
    get: () => sortBy(props.modelValue, 'weight'),
    set: (value: Component[]) => emits('update:modelValue', value)
});

const getComponentHash = (component: Component): string => {
    return hash(component.component_fields.sort((a, b) => a.weight - b.weight));
};
</script>
