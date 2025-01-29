<template>
    <div>
        <label class="font-medium mb-2 block">{{ field.label }}</label>
        <div>
            <input
                type="file"
            >
        </div>
    </div>
</template>

<script setup lang="ts">
import type {Field} from '@modules/page_builder/assets/js/types'

defineProps<{
    field: Field
}>()
</script>
