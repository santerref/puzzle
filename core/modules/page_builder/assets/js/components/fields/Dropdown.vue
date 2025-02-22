<template>
    <div>
        <label class="font-medium mb-2 block">{{ field.label }}</label>
        <div>
            <select
                v-model="model"
                class="border shadow w-full p-2 bg-white border-grey-400"
            >
                <option
                    v-for="(option, value) in field.options"
                    :key="value"
                    :value="value"
                >
                    {{ option }}
                </option>
            </select>
        </div>
    </div>
</template>

<script setup lang="ts">
import type {ComponentField} from '@modules/page_builder/assets/js/types/page-builder';

defineProps<{
    field: ComponentField
}>();
const model = defineModel<string>();
</script>
