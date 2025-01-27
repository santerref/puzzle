<template>
    <div>
        <div
            :class="{'bg-teal-100':component.isDirty}"
            class="bg-blue-100 p-5 relative"
            @mouseenter="showToolbar = true"
            @mouseleave="showToolbar = false"
        >
            <!-- eslint-disable-next-line vue/no-v-html -->
            <div v-html="component.user.html"/>
            <div
                v-if="showToolbar"
                class="bg-red-100 flex gap-4 absolute top-0 left-0 px-4 py-2 shadow"
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
                    v-if="component.isDirty"
                    class="pi pi-undo hover:cursor-pointer"
                    @click.prevent="undo(component)"
                />
                <i
                    v-if="component.weight > 1"
                    class="pi pi-angle-up hover:cursor-pointer"
                    @click.prevent="components.moveUp(component)"
                />
                <i
                    v-if="component.weight < components.editorComponents.length"
                    class="pi pi-angle-down hover:cursor-pointer"
                    @click.prevent="components.moveDown(component)"
                />
            </div>
        </div>
        <editor
            v-if="edit"
            v-model="edit"
            :component="component"
        />
    </div>
</template>

<script setup lang="ts">
import {ref} from 'vue'
import Editor from '@/js/components/Editor.vue'
import {useComponentsStore} from '@/js/stores/components'
import {EditorComponent} from '@/js/types'

defineProps<{
    component: EditorComponent
}>()
const showToolbar = ref(false)

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
