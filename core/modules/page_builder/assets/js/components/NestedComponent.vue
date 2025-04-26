<template>
    <draggable
        v-model="draggableComponents"
        handle=".handle"
        @start="drag=true"
        @end="drag=false"
        @dragover.prevent
        @dragenter.prevent
        :item-key="itemKey"
        :group="{ name: 'pagebuilder', pull: false, put: false }"
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
    </draggable>
</template>

<script setup lang="ts">
import type {Component} from '@modules/page_builder/assets/js/types/page-builder';
import {computed, ref} from 'vue';
import Item from '@modules/page_builder/assets/js/components/Item.vue';
import hash from 'object-hash';
import draggable from 'vuedraggable';

const drag = ref(false);

const props = defineProps<{
    modelValue: Component[]
}>();

const itemKey = function (element) {
    return getComponentHash(element);
}

const emits = defineEmits<{
    (e: 'update:modelValue', value: Component[]): void
}>();

const draggableComponents = computed({
    get: () => props.modelValue,
    set: (value: Component[]) => emits('update:modelValue', value)
});

const getComponentHash = (component: Component): string => {
    return hash(component.component_fields.sort((a, b) => a.weight - b.weight));
};
</script>
