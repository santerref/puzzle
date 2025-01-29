<template>
    <div>
        <label class="font-medium mb-2 block">{{ field.label }}</label>
        <div>
            <input
                v-model="model"
                class="border shadow w-full p-2 bg-white border-grey-400"
                type="text"
            >
        </div>
    </div>
</template>

<script setup lang="ts">
import type {Field} from '@modules/page_builder/assets/js/types'

defineProps<{
    field: Field
}>()
const model = defineModel<string>()
</script>
