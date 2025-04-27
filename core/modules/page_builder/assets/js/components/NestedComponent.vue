<template>
    <draggable
        v-model="draggableComponents"
        handle=".handle"
        :item-key="itemKey"
        :group="group"
        @change="updateParent"
        @add="hideHeader = true"
        @dragenter="hideHeader = true"
        @remove="hideHeader = false"
    >
        <template #item="{element}">
            <div>
                <item
                    :id="element.id"
                    :component-count="draggableComponents.length"
                    :component-uuid="element.id"
                />
            </div>
        </template>
        <template #header>
            <div class="relative" v-if="draggableComponents.length === 0 && container && !hideHeader">
                <div class="p-5 absolute left-0 right-0 top-0 flex opacity-70 justify-center">
                    <p class="text-center px-6 py-4 rounded text-stone-600 font-medium m-auto d-block bg-stone-100">
                        Drag or click here to add components to the container
                    </p>
                </div>
            </div>
        </template>
    </draggable>
</template>

<script setup lang="ts">
import type {Component} from '@modules/page_builder/assets/js/types/page-builder';
import {computed, ref} from 'vue';
import Item from '@modules/page_builder/assets/js/components/Item.vue';
import hash from 'object-hash';
import draggable from 'vuedraggable/src/vuedraggable';

const updateParent = function (event) {
    if (props.component && event.hasOwnProperty('added')) {
        if (event.added.element.parent !== props.component.id) {
            event.added.element.parent = props.component.id;
        }
    }
};

const hideHeader = ref(false)

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

const move = function(evt, originalEvent) {
   alert('bob')
}

const draggableComponents = computed({
    get: () => props.modelValue,
    set: (value: Component[]) => emits('update:modelValue', value)
});

const getComponentHash = (component: Component): string => {
    return hash(component.component_fields.sort((a, b) => a.weight - b.weight));
};
</script>
