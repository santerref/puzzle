<template>
    <div>
        <label class="font-medium mb-2 block">{{ field.label }}</label>
        <div>
            <div class="w-[150px] rounded mb-4 h-[150px] bg-stone-300"/>

            <input
                ref="file"
                type="file"
                accept=".png, .jpg, .jpeg, .gif"
                @change="uploadFile"
            >
        </div>
    </div>
</template>

<script setup lang="ts">
import type {Field} from '@modules/page_builder/assets/js/types'
import {useTemplateRef} from 'vue'

const model = defineModel<string>()

defineProps<{
    field: Field
}>()

const fileRef = useTemplateRef('file')

async function uploadFile(event: any) {
    if (event.target.files.length) {
        const file = event.target.files[0]
        const formData = new FormData()
        formData.append('image', file)
        try {
            const response = await fetch('/admin/files/upload/image', {
                method: 'POST',
                body: formData,
            })

            const result = await response.json()
            model.value = result.id
            // eslint-disable-next-line @typescript-eslint/no-unused-vars
        } catch (error) {
        }
        fileRef.value.value = null
    }
}
</script>
