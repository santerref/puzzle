<template>
    <div class="fixed z-30 top-0 right-0 bottom-0 w-[700px] shadow-md p-5 bg-teal-100 max-h-full overflow-auto">
        <div class="flex flex-col min-h-full">
            <div class="flex-grow">
                <h1 class="text-lg mb-4">
                    Edit <strong>{{ component.user.name }}</strong>
                </h1>
                <div class="space-y-4">
                    <component
                        :is="changeCase.pascalCase(field.type)"
                        v-for="(field,key) in component.user.fields"
                        :key="key"
                        v-model="form[key]"
                        :field="field"
                    />
                </div>
            </div>
            <div class="flex gap-4 mt-4 flex-shrink-0">
                <button
                    class="bg-black cursor-pointer text-white px-4 py-2 font-bold"
                    @click.prevent="save"
                >
                    Save
                </button>
                <button
                    class="bg-gray-300 cursor-pointer px-4 py-2 font-bold"
                    @click.prevent="model = false"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {ref} from 'vue'
import {useComponentsStore} from '@/js/stores/components'
import clone from 'clone-deep'
import type {EditorComponent} from '@/js/types'
import * as changeCase from 'change-case'

const components = useComponentsStore()
const props = defineProps<{
    component: EditorComponent
}>()

const model = defineModel<boolean>()

const form = ref({})
for (const [key, value] of Object.entries(props.component.user.fields)) {
    Object.assign(form.value, {
        [key]: value.value
    })
}

const save = async function () {
    try {
        const response = await fetch(`/api/components/${props.component.user.id}/update`, {
            method: 'PUT',
            body: JSON.stringify({
                fields: form.value
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        let component = await response.json()
        let editorComponent = clone(props.component)
        editorComponent.user.html = component.html
        Object.assign(editorComponent.user, component.component)
        components.update(editorComponent)
        model.value = false
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
    } catch (e) {

    }
}
</script>
