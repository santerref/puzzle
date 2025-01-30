<template>
    <div class="bg-stone-100 w-full p-3 flex justify-end">
        <button
            :class="{
                'cursor-not-allowed opacity-50': !components.isDirty,
                'cursor-pointer': components.isDirty,
            }"
            :disabled="!components.isDirty"
            @click.prevent="components.save"
            class="text-white bg-stone-600 px-4 py-2 font-medium"
        >
            Save
        </button>
    </div>
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3 p-4">
            <ul class="grid grid-cols-2 gap-4">
                <li
                    v-for="(component, key) in components.all"
                    :key="key"
                >
                    <button
                        class=" border border-stone-100 text-stone-600 text-sm font-medium uppercase rounded shadow block w-full py-2 px-4 cursor-pointer min-h-[100px]"
                        @click.prevent="addComponent(key, component)"
                    >
                        {{ component.name }}
                    </button>
                </li>
            </ul>
        </div>
        <div class="col-span-9 p-4">
            <div class="container m-auto">
                <div class="border border-dashed border-stone-400 rounded-2xl p-5">
                    <VueDraggable
                        v-if="components.allItems.length > 0"
                        :model-value="components.allItems"
                        handle=".handle"
                        @update:model-value="components.updateEditors"
                    >
                        <item
                            v-for="(pageBuilderItem, key) in components.allItems"
                            :key="key"
                            :component="pageBuilderItem"
                        />
                    </VueDraggable>
                    <div
                        v-else
                        class="flex items-center"
                    >
                        <p class="text-center px-6 py-4 rounded text-stone-600 font-medium m-auto d-block bg-stone-100">
                            Add your first component.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Item from '@modules/page_builder/assets/js/components/Item.vue'
import {useComponentsStore} from '@modules/page_builder/assets/js/stores/components'
import {VueDraggable} from 'vue-draggable-plus'

const components = useComponentsStore()
components.initialize()

const addComponent = function (id, component) {
    components.add(id, component)
}
</script>

<style lang="scss">

</style>
