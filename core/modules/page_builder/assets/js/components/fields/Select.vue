<template>
    <div>
        <label class="tw:font-medium tw:mb-2 tw:block">{{ fieldType.label }}</label>
        <div>
            <select
                v-model="model"
                class="tw:border tw:shadow tw:w-full tw:p-2 tw:bg-white tw:border-grey-400"
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

const fieldType = computed(() => props.componentType.fields.find((field) => field.id === props.field.field_name));
</script>
