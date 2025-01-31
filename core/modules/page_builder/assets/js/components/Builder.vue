<template>
    <div class="bg-stone-100 w-full p-3 flex justify-end">
        <button
            :class="{
                'cursor-not-allowed opacity-50': !components.isDirty,
                'cursor-pointer': components.isDirty,
            }"
            :disabled="!components.isDirty"
            class="text-white bg-stone-600 px-4 py-2 font-medium"
            @click.prevent="components.save"
        >
            Save
        </button>
    </div>
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3 p-4">
            <div class="border-2 mb-4 p-5 border-stone-200">
                <p class="font-semibold mb-2">
                    Tree
                </p>
                <ul class="mt-2">
                    <li>
                        <button
                            class="cursor-pointer text-blue-500 underline"
                            :class="{'bg-stone-200':active}"
                            @click.prevent="components.setCurrentComponent(null, null)"
                        >
                            Root
                        </button>
                        <ul class="pl-4 list-disc">
                            <TreeItem
                                v-for="pageBuilderItem in components.rootItems"
                                :key="pageBuilderItem.live.id"
                                :page-builder-item="pageBuilderItem"
                            />
                        </ul>
                    </li>
                </ul>
            </div>

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
                        v-if="components.rootItems.length > 0"
                        :model-value="components.rootItems"
                        handle=".handle"
                        @end="onDragEnd"
                        @update:model-value="components.updateEditors"
                    >
                        <item
                            v-for="pageBuilderItem in components.rootItems"
                            :key="pageBuilderItem.live.id"
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
    <editor
        v-if="components.currentEdit !== null"
        :component="components.currentEdit"
    />
</template>

<script setup lang="ts">
import Item from '@modules/page_builder/assets/js/components/Item.vue'
import {useComponentsStore} from '@modules/page_builder/assets/js/stores/components'
import {VueDraggable} from 'vue-draggable-plus'
import TreeItem from '@modules/page_builder/assets/js/components/TreeItem.vue'
import {computed, nextTick} from 'vue'
import {PageBuilderItem} from '@modules/page_builder/assets/js/types'
import Editor from '@modules/page_builder/assets/js/components/Editor.vue'

const components = useComponentsStore()
components.initialize()

const addComponent = function (id, component) {
    components.add(id, component)
}

const onDragEnd = () => {
    nextTick(() => {
        components.rootItems.forEach((item: PageBuilderItem) => {
            item.rerender = !item.rerender // Trigger reactivity
        })
    })
}

const active = computed(() => components.currentComponent === null)
</script>

<style lang="scss">

</style>
