import {defineStore} from 'pinia'
import {computed, ref} from 'vue'
import equal from 'deep-equal'
import {ComponentType, PageBuilderItem, PageComponent} from '@modules/page_builder/assets/js/types'
import {v4 as uuidv4} from 'uuid'
import clone from 'clone-deep'

export const useComponentsStore = defineStore('components', () => {
    const components = ref<ComponentType[]>([])
    const originalPageBuilderItems = ref<PageBuilderItem[]>([])
    const pageBuilderItems = ref<PageBuilderItem[]>([])
    const currentPageUuid = ref<string>(window.page_uuid)

    const all = computed(() => components.value)
    const allItems = computed(() => pageBuilderItems.value.sort((a, b) => a.live.weight - b.live.weight))

    const isDirty = computed(() => {
        let dirty = false
        pageBuilderItems.value.forEach((pageBuilderItem: PageBuilderItem) => {
            if (pageBuilderItem.isDirty()) {
                dirty = true
                return
            }
        })
        //@TODO: Mabye only use this?
        if (!equal(originalPageBuilderItems.value, pageBuilderItems.value)) {
            dirty = true
        }
        return dirty
    })

    function setCurrentPageUuid(pageUuid: string) {
        currentPageUuid.value = pageUuid
    }

    async function initialize() {
        const [pageResponse, componentsResponse] = await Promise.all([
            fetch(`/api/pages/${currentPageUuid.value}`),
            fetch('/api/components')
        ])
        const page = await pageResponse.json()
        components.value = await componentsResponse.json()
        loadPageComponents(page)
        pageBuilderItems.value = clone(originalPageBuilderItems.value)
    }

    function loadPageComponents(page: any) {
        originalPageBuilderItems.value = []
        page.components.forEach((component: PageComponent) => {
            originalPageBuilderItems.value.push({
                isNew: false,
                live: clone(component as PageComponent),
                original: clone(component as PageComponent),
                isDirty() {
                    return this.isNew || !equal(this.live, this.original)
                }
            } as PageBuilderItem)
        })
        originalPageBuilderItems.value.sort((a, b) => a.live.weight - b.live.weight)
    }

    function moveUp(component: PageBuilderItem) {
        const index = pageBuilderItems.value.findIndex(obj => obj.live.id === component.live.id)
        if (index > 0) {
            ;[pageBuilderItems.value[index - 1], pageBuilderItems.value[index]] =
                [pageBuilderItems.value[index], pageBuilderItems.value[index - 1]]

            ;[pageBuilderItems.value[index - 1].live.weight, pageBuilderItems.value[index].live.weight] =
                [pageBuilderItems.value[index].live.weight, pageBuilderItems.value[index - 1].live.weight]
        }
    }

    function moveDown(component: PageBuilderItem) {
        const index = pageBuilderItems.value.findIndex(obj => obj.live.id === component.live.id)

        if (index < pageBuilderItems.value.length - 1) { // Ensure there's an element to swap with
            ;[pageBuilderItems.value[index], pageBuilderItems.value[index + 1]] =
                [pageBuilderItems.value[index + 1], pageBuilderItems.value[index]]

            ;[pageBuilderItems.value[index].live.weight, pageBuilderItems.value[index + 1].live.weight] =
                [pageBuilderItems.value[index + 1].live.weight, pageBuilderItems.value[index].live.weight]
        }
    }

    function remove(component: PageBuilderItem) {
        const index = pageBuilderItems.value.findIndex(obj => obj.live.id === component.live.id)
        if (index !== -1) {
            pageBuilderItems.value.splice(index, 1)
        }
    }

    async function rerender(component: PageBuilderItem) {
        try {
            const response = await fetch(`/api/components/${component.live.component_type}/refresh`, {
                method: 'PUT',
                body: JSON.stringify({
                    form_values: component.live.form_values
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            const pageComponent = await response.json()
            update(component.live, pageComponent)
            // eslint-disable-next-line @typescript-eslint/no-unused-vars
        } catch (e) {

        }
    }

    function undo(component: PageBuilderItem) {
        Object.assign(component.live, component.original)
    }

    function update(component: PageComponent, updatedData: Partial<PageComponent>) {
        const index = pageBuilderItems.value.findIndex(obj => obj.live.id === component.id)
        Object.assign(pageBuilderItems.value[index].live, updatedData)
    }

    async function add(id: string) {
        let data: Partial<PageComponent> = {}
        try {
            const response = await fetch(`/api/components/${id}/render`, {
                method: 'POST'
            })
            data = await response.json()
            // eslint-disable-next-line @typescript-eslint/no-unused-vars
        } catch (e) {

        }

        data.id = `temporary:${uuidv4()}`
        data.weight = pageBuilderItems.value.length + 1

        pageBuilderItems.value.push({
            isNew: true,
            live: clone(data as PageComponent),
            original: clone(data as PageComponent),
            isDirty() {
                return this.isNew || !equal(this.live, this.original)
            }
        } as PageBuilderItem)
    }

    function updateEditors(components: PageBuilderItem[]) {
        components.forEach((component, index) => {
            component.live.weight = index + 1
            return component
        })
        pageBuilderItems.value = components
    }

    async function save() {
        try {
            const response = await fetch(`/api/pages/${currentPageUuid.value}/components`, {
                method: 'PUT',
                body: JSON.stringify(pageBuilderItems.value.map(pageBuilderItem => pageBuilderItem.live))
            })
            const page = await response.json()
            loadPageComponents(page)
            pageBuilderItems.value = clone(originalPageBuilderItems.value)
            // eslint-disable-next-line @typescript-eslint/no-unused-vars
        } catch (e) {

        }
    }

    return {
        components,
        pageBuilderItems,
        originalPageBuilderItems,
        isDirty,
        rerender,
        add,
        save,
        all,
        setCurrentPageUuid,
        allItems,
        currentPageUuid,
        update,
        remove,
        undo,
        updateEditors,
        moveUp,
        moveDown,
        initialize
    }
})
