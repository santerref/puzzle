<template>
    <VueDraggable
        v-model="draggableComponents"
        handle=".handle"
    >
        <item
            v-for="component in modelValue"
            :key="getComponentHash(component)"
            :component-count="modelValue.length"
            :component-uuid="component.id"
        />
    </VueDraggable>
</template>

<script setup lang="ts">
import type {Component} from '@modules/page_builder/assets/js/types/page-builder';
import {computed} from 'vue';
import {VueDraggable} from 'vue-draggable-plus';
import Item from '@modules/page_builder/assets/js/components/Item.vue';
import hash from 'object-hash';

const props = defineProps<{
    modelValue: Component[]
}>();

const emits = defineEmits<{
    (e: 'update:modelValue', value: Component[]): void
}>();

const draggableComponents = computed({
    get: () => props.modelValue,
    set: (value: Component[]) => emits('update:modelValue', value)
});

const getComponentHash = (component: Component): string => {
    return hash(component.component_fields);
};
</script>
