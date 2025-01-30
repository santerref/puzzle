<template>
    <div class="fixed z-30 top-0 right-0 bottom-0 w-[700px] shadow-md p-5 bg-stone-50 max-h-full overflow-auto">
        <div class="flex flex-col min-h-full">
            <div class="flex-grow">
                <h1 class="text-lg mb-4">
                    Edit <strong>{{ componentType.name }}</strong>
                </h1>
                <div class="space-y-4">
                    <component
                        :is="changeCase.pascalCase(field.type)"
                        v-for="(field,key) in componentType.settings.fields"
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
import {computed} from 'vue'
import {useComponentsStore} from '@modules/page_builder/assets/js/stores/components'
import type {PageComponent} from '@modules/page_builder/assets/js/types'
import * as changeCase from 'change-case'

const components = useComponentsStore()
const props = defineProps<{
    component: PageComponent
}>()

const model = defineModel<boolean>()
const componentType = computed(() => components.components[props.component.component_type])
const form = props.component.form_values

const save = async function () {
    try {
        const response = await fetch(`/api/components/${props.component.component_type}/refresh`, {
            method: 'PUT',
            body: JSON.stringify({
                form_values: form
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        const pageComponent = await response.json()
        components.update(props.component, pageComponent)
        model.value = false
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
    } catch (e) {

    }
}
</script>
