import {defineStore} from 'pinia'
import {computed, ref} from 'vue'
import equal from 'deep-equal'
import clone from 'clone-deep'
import {v4 as uuidv4} from 'uuid'
import {Component, EditorComponent} from '@modules/page_builder/assets/js/types'

export const useComponentsStore = defineStore('components', () => {
    const components = ref<Component[]>([])
    const pageComponents = ref<EditorComponent[]>([])
    const editorComponents = ref<EditorComponent[]>([])

    const all = computed(() => components.value)
    const allEditor = computed(() => editorComponents.value)

    const isDirty = computed(() => {
        let dirty = false
        editorComponents.value.forEach(component => {
            if (!component.isNew) {
                component.isDirty = !equal(component.original, component.user)
                if (component.isDirty || component.weight !== component.originalWeight) {
                    dirty = true
                }
            }
        })
        if (!dirty && !equal(pageComponents.value, editorComponents.value)) {
            dirty = true
        }
        return dirty
    })

    async function initialize(page_uuid: string) {
        const [pageResponse, componentsResponse] = await Promise.all([
            fetch(`/api/pages/${page_uuid}`),
            fetch('/api/components')
        ])
        const page = await pageResponse.json()
        components.value = await componentsResponse.json()
        pageComponents.value = page.components
        editorComponents.value = clone(pageComponents.value)
    }

    function moveUp(component: EditorComponent) {
        const index = editorComponents.value.findIndex(obj => obj.id === component.id)
        if (index > 0) {
            ;[editorComponents.value[index - 1], editorComponents.value[index]] =
                [editorComponents.value[index], editorComponents.value[index - 1]]

            ;[editorComponents.value[index - 1].weight, editorComponents.value[index].weight] =
                [editorComponents.value[index].weight, editorComponents.value[index - 1].weight]
        }
    }

    function moveDown(component: EditorComponent) {
        const index = editorComponents.value.findIndex(obj => obj.id === component.id)

        if (index < editorComponents.value.length - 1) { // Ensure there's an element to swap with
            ;[editorComponents.value[index], editorComponents.value[index + 1]] =
                [editorComponents.value[index + 1], editorComponents.value[index]]

            ;[editorComponents.value[index].weight, editorComponents.value[index + 1].weight] =
                [editorComponents.value[index + 1].weight, editorComponents.value[index].weight]
        }
    }

    function remove(component: EditorComponent) {
        const index = editorComponents.value.findIndex(obj => obj.id === component.id)
        editorComponents.value.splice(index, 1)
    }

    function undo(component: EditorComponent) {
        if (component.isNew) {
            return
        }
        const index = editorComponents.value.findIndex(obj => obj.id === component.id)
        Object.assign(editorComponents.value[index].user, editorComponents.value[index].original)
        editorComponents.value[index].isDirty = false
    }

    function update(component: EditorComponent) {
        const index = editorComponents.value.findIndex(obj => obj.id === component.id)
        component.isDirty = !component.isNew && !equal(component.original, component.user)
        editorComponents.value.splice(index, 1, component)
    }

    async function add(id: string) {
        let html = ''
        let data: any = {}
        try {
            const response = await fetch(`/api/components/${id}/render`)
            data = await response.json()
            // eslint-disable-next-line @typescript-eslint/no-unused-vars
        } catch (e) {

        }

        const component = data.component as Component
        html = data.html

        // eslint-disable-next-line @typescript-eslint/no-unused-vars
        for (const [key, value] of Object.entries(component.fields)) {
            value.value = value.default_value
        }
        const weight = editorComponents.value.length + 1
        const newComponent = {
            id: uuidv4(),
            user: clone(component),
            isDirty: false,
            weight: weight,
            isNew: true
        }
        newComponent.user.html = html
        editorComponents.value.push(newComponent)
    }

    function updateEditors(components: EditorComponent[]) {
        components.forEach((component, index) => {
            component.weight = index + 1
            return component
        })
        editorComponents.value = components
    }

    return {
        components,
        editorComponents,
        pageComponents,
        isDirty,
        add,
        all,
        allEditor,
        update,
        remove,
        undo,
        updateEditors,
        moveUp,
        moveDown,
        initialize
    }
})
