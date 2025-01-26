<template>
    <div>
        <label class="font-medium mb-2 block">{{ field.label }}</label>
        <div>
            <input v-model="model" class="border shadow w-full p-2 bg-white border-grey-400" type="text"/>
        </div>
    </div>
</template>

<script setup>
defineProps(['field', 'component'])
const model = defineModel()
</script>
