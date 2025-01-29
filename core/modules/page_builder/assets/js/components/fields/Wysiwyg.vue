<template>
    <div>
        <label class="font-medium mb-2 block">{{ field.label }}</label>
        <ckeditor
            v-if="editor"
            v-model="model"
            :config="config"
            :editor="editor"
        />
    </div>
</template>

<script setup lang="ts">
import {computed} from 'vue'
import {Ckeditor, useCKEditorCloud} from '@ckeditor/ckeditor5-vue'
import type {Field} from '@modules/page_builder/assets/js/types'

const model = defineModel<string>()
defineProps<{
    field: Field
}>()

const cloud = useCKEditorCloud({
    version: '44.1.0',
    premium: false
})
const editor = computed(() => {
    if (!cloud.data.value) {
        return null
    }

    return cloud.data.value.CKEditor.ClassicEditor
})
const config = computed(() => {
    if (!cloud.data.value) {
        return null
    }

    const {Essentials, Paragraph, Bold, Italic} = cloud.data.value.CKEditor

    return {
        licenseKey: 'eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE3Njk0NzE5OTksImp0aSI6IjJlZjBjMGYyLTk1N2YtNDYyNy1hYTM5LWI2NDQwZThhODc2MyIsInVzYWdlRW5kcG9pbnQiOiJodHRwczovL3Byb3h5LWV2ZW50LmNrZWRpdG9yLmNvbSIsImRpc3RyaWJ1dGlvbkNoYW5uZWwiOlsiY2xvdWQiLCJkcnVwYWwiXSwiZmVhdHVyZXMiOlsiRFJVUCJdLCJ2YyI6IjFiNDY3OTM4In0.odi9YmcLJTcjzR95sDoqssCI_zEdo2NkxGB6fHeyp-7EBGUkB0VRZXjcawpGD6nfLUa-SQKw6299uI8AVQZOhw',
        plugins: [Essentials, Paragraph, Bold, Italic],
        toolbar: ['undo', 'redo', '|', 'bold', 'italic']
    }
})
</script>
