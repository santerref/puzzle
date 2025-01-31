<template>
    <div>
        <div
            class="relative hover:outline-2"
            :class="{'hover:outline-stone-800':!component.live.container,'hover:outline-blue-800':component.live.container}"
            @mouseenter="showToolbar = true"
            @mouseleave="showToolbar = false"
        >
            <!-- eslint-disable vue/no-v-html -->
            <div
                ref="html"
                v-html="component.live.rendered_html"
            />
            <div
                v-if="showToolbar"
                class="text-stone-100 flex gap-4 py-3 absolute top-0 rounded-bl-md px-4 shadow-lg"
                :class="{'-right-0.5 bg-stone-800':!component.live.container,'-left-0.5 bg-blue-800':component.live.container}"
            >
                <i class="pi handle text-stone-100 pi-arrows-alt hover:cursor-grab"/>
                <i
                    class="pi pi-pencil hover:cursor-pointer"
                    @click.prevent="components.editComponent(component)"
                />
                <i
                    class="pi text-stone-100 pi-trash hover:cursor-pointer"
                    @click.prevent="remove(component)"
                />
                <i
                    class="pi text-stone-100 pi-sync hover:cursor-pointer"
                    @click.prevent="components.rerender(component)"
                />
                <i
                    v-if="isDirty"
                    class="pi text-stone-100 pi-undo hover:cursor-pointer"
                    @click.prevent="undo(component)"
                />
                <i
                    v-if="component.live.weight > 1"
                    class="pi text-stone-100 pi-angle-up hover:cursor-pointer"
                    @click.prevent="components.moveUp(component)"
                />
                <i
                    v-if="component.live.weight < components.pageBuilderItems.length"
                    class="pi text-stone-100 pi-angle-down hover:cursor-pointer"
                    @click.prevent="components.moveDown(component)"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {computed, h, nextTick, onMounted, ref, render, useTemplateRef, watch} from 'vue'
import {useComponentsStore} from '@modules/page_builder/assets/js/stores/components'
import {PageBuilderItem} from '@modules/page_builder/assets/js/types'
import equal from 'deep-equal'
import Children from '@modules/page_builder/assets/js/components/Children.vue'

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

const renderChildren = () => {
    if (html.value) {
        const childrenElements = html.value.querySelectorAll(`[id="${props.component.live.id}"]`)
        childrenElements.forEach((el) => {
            render(
                h(Children, {componentUuid: props.component.live.id, key: props.component.live.id}),
                el
            )
        })
    }
}

const html = useTemplateRef('html')
onMounted(renderChildren)

watch(() => props.component.rerender, async (rerender) => {
    if (rerender) {
        await nextTick()
        renderChildren()
        //@TODO: Add a method in the store to rerender.
        // eslint-disable-next-line vue/no-mutating-props
        props.component.rerender = false
    }
})
</script>
