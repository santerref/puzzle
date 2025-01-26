import {defineStore} from 'pinia'
import {computed, ref} from "vue"
import equal from 'deep-equal'
import clone from 'clone-deep'
import {v4 as uuidv4} from 'uuid'

export const useComponentsStore = defineStore('components', () => {
    const components = ref([])
    const editorComponents = ref([])

    const all = computed(() => components.value)
    const allEditor = computed(() => editorComponents.value)

    const isDirty = computed(() => {
        let dirty = false
        editorComponents.value.forEach(component => {
            component.isDirty = !equal(component.original, component.user)
            if (component.isDirty || component.weight !== component.originalWeight) {
                dirty = true
            }
        })
        return dirty
    })

    async function load() {
        try {
            const response = await fetch('/api/components')
            components.value = await response.json()
        } catch (e) {

        }
    }

    function remove(component) {
        const index = editorComponents.value.findIndex(obj => obj.id === component.id)
        editorComponents.value.splice(index, 1)
    }

    function undo(component) {
        const index = editorComponents.value.findIndex(obj => obj.id === component.id)
        Object.assign(editorComponents.value[index].user, editorComponents.value[index].original)
        editorComponents.value[index].isDirty = false
    }

    function update(component) {
        const index = editorComponents.value.findIndex(obj => obj.id === component.id)
        component.isDirty = !equal(component.original, component.user)
        editorComponents.value.splice(index, 1, component)
    }

    async function add(id) {
        let html = ''
        let data = {}
        try {
            const response = await fetch(`/api/components/${id}/render`)
            data = await response.json()
        } catch (e) {

        }

        let component = data.component
        html = data.html

        for (const [key, value] of Object.entries(component.fields)) {
            value.value = value.default_value
        }
        const weight = editorComponents.value.length + 1
        let newComponent = {
            id: uuidv4(),
            original: clone(component),
            user: clone(component),
            isDirty: false,
            weight: weight,
            originalWeight: weight
        }
        newComponent.original.html = html
        newComponent.user.html = html
        editorComponents.value.push(newComponent)
    }

    function updateEditors(components) {
        components.forEach((component, index) => {
            component.weight = index + 1
            return component
        })
        editorComponents.value = components
    }

    return {
        components,
        editorComponents,
        isDirty,
        load,
        add,
        all,
        allEditor,
        update,
        remove,
        undo,
        updateEditors
    }
})
