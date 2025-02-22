<template>
    <div>
        <label class="font-medium mb-2 block">{{ fieldType.label }}</label>
        <div>
            <select
                v-model="model"
                class="border shadow w-full p-2 bg-white border-grey-400"
            >
                <option
                    v-for="(option, value) in fieldType.settings?.options"
                    :key="value"
                    :value="value"
                >
                    {{ option }}
                </option>
            </select>
        </div>
    </div>
</template>

<script setup lang='ts'>
import type {ComponentField, ComponentType} from '@modules/page_builder/assets/js/types/page-builder';
import {computed} from 'vue';

const props = defineProps<{
    field: ComponentField,
    componentType: ComponentType
}>();
const model = defineModel<string>();

const fieldType = computed(() => props.componentType.fields[props.field.field_name]);
</script>
