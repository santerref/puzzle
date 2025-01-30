<template>
    <div>
        <div
            class="relative hover:outline-2 hover:outline-stone-200"
            @mouseenter="showToolbar = true"
            @mouseleave="showToolbar = false"
        >
            <!-- eslint-disable-next-line vue/no-v-html -->
            <div v-html="component.live.rendered_html"/>
            <div
                v-if="showToolbar"
                class="bg-stone-100 flex gap-4 absolute -translate-y-full top-0 rounded-tr-md rounded-tl-md left-0 px-4 py-2 shadow"
            >
                <i class="pi handle pi-arrows-alt hover:cursor-grab"/>
                <i
                    class="pi pi-pencil hover:cursor-pointer"
                    @click.prevent="edit=true"
                />
                <i
                    class="pi pi-trash hover:cursor-pointer"
                    @click.prevent="remove(component)"
                />
                <i
                    v-if="isDirty"
                    class="pi pi-undo hover:cursor-pointer"
                    @click.prevent="undo(component)"
                />
                <i
                    v-if="component.live.weight > 1"
                    class="pi pi-angle-up hover:cursor-pointer"
                    @click.prevent="components.moveUp(component)"
                />
                <i
                    v-if="component.live.weight < components.pageBuilderItems.length"
                    class="pi pi-angle-down hover:cursor-pointer"
                    @click.prevent="components.moveDown(component)"
                />
            </div>
        </div>
        <editor
            v-if="edit"
            v-model="edit"
            :component="component.live"
        />
    </div>
</template>

<script setup lang="ts">
import {computed, ref} from 'vue'
import Editor from '@modules/page_builder/assets/js/components/Editor.vue'
import {useComponentsStore} from '@modules/page_builder/assets/js/stores/components'
import {PageBuilderItem} from '@modules/page_builder/assets/js/types'
import equal from 'deep-equal'

const props = defineProps<{
    component: PageBuilderItem
}>()
const showToolbar = ref(false)
const isDirty = computed(() => !equal(props.component.live, props.component.original))

const components = useComponentsStore()

function remove(component) {
    if (confirm('Do you really want to remove this component?')) {
        components.remove(component)
    }
}

function undo(component) {
    if (confirm('Do you really want to undo the changes?')) {
        components.undo(component)
    }
}

const edit = ref(false)
</script>
