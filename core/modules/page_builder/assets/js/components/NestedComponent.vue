<template>
    <VueDraggable
        v-model="draggableComponents"
        handle=".handle"
    >
        <item
            v-for="component in modelValue"
            :key="`${component.id}`"
            :component-uuid="component.id"
        />
    </VueDraggable>
</template>

<script setup lang="ts">
import {Component} from "@modules/page_builder/assets/js/types/page-builder";
import {computed} from "vue";
import {VueDraggable} from "vue-draggable-plus";
import Item from "@modules/page_builder/assets/js/components/Item.vue";

const props = defineProps<{
    modelValue: Component[]
}>();

const emits = defineEmits<{
    (e: 'update:modelValue', value: Component[]): void
}>()

const draggableComponents = computed({
    get: () => props.modelValue,
    set: (value: Component[]) => emits('update:modelValue', value)
})
</script>
