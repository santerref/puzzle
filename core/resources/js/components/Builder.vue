<template>
    <div class="bg-gray-100 w-full p-5 flex justify-end">
        <button :class="{'cursor-not-allowed opacity-50':!components.isDirty, 'cursor-pointer':components.isDirty}"
                :disabled="!components.isDirty"
                class="text-white bg-teal-500 px-4 py-2 font-bold">Publish
        </button>
    </div>
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3 p-4">
            <ul class="space-y-4">
                <li v-for="(component, key) in components.all" :key="key">
                    <button class="bg-gray-100 block w-full py-2 px-4 cursor-pointer"
                            @click.prevent="addComponent(key, component)">
                        {{ component.name }}
                    </button>
                </li>
            </ul>
        </div>
        <div class="col-span-9 p-4">
            <div class="container m-auto">
                <div :class="{'bg-orange-50':components.isDirty}" class="border border-dashed border-grey-100 p-5">
                    <VueDraggable :model-value="components.allEditor" class="space-y-4"
                                  handle=".handle" @update:modelValue="components.updateEditors">
                        <item v-for="(component, key) in components.allEditor" :key="key" :component="component"/>
                    </VueDraggable>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup lang="ts">
import Item from '@/components/Item.vue'
import {useComponentsStore} from '@/stores/components'
import {VueDraggable} from 'vue-draggable-plus'

const components = useComponentsStore()
components.load()

const addComponent = function (id, component) {
    components.add(id, component)
}
</script>

<style lang="scss">

</style>
