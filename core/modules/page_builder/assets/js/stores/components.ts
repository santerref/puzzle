import {defineStore} from 'pinia'
import {computed, ref} from 'vue'
import equal from 'deep-equal'
import {ComponentType, PageBuilderItem, PageComponent} from '@modules/page_builder/assets/js/types'
import clone from 'clone-deep'

export const useComponentsStore = defineStore('components', () => {
    const components = ref<ComponentType[]>([])
    const originalPageBuilderItems = ref<PageBuilderItem[]>([])
    const pageBuilderItems = ref<PageBuilderItem[]>([])
    const currentPageUuid = ref<string>(window.page_uuid)
    const currentComponent = ref<PageBuilderItem | null>(null)
    const currentEdit = ref<PageBuilderItem | null>(null)

    const all = computed(() => {
        if (currentComponent.value === null) {
            return Object.entries(components.value).reduce((acc, [key, component]) => {
                if (component.container === true) {
                    acc[key] = component // Keep the key-value pair if it matches
                }
                return acc
            }, {})
        }
        return components.value
    })
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

    const rootItems = computed(() => allItems.value.filter(item => item.live.parent === null))

    function getChildren(pageBuilderItem: PageBuilderItem): PageBuilderItem[] {
        return pageBuilderItems.value.filter(item => item.live.parent === pageBuilderItem.live.id)
    }

    function editComponent(pageComponent: PageBuilderItem | null) {
        currentEdit.value = pageComponent
    }

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
                rerender: false,
                live: clone(component as PageComponent),
                original: clone(component as PageComponent),
                isDirty() {
                    return this.isNew || !equal(this.live, this.original)
                },
                children() {
                    return getChildren(this)
                }
            } as PageBuilderItem)
        })
        originalPageBuilderItems.value.sort((a, b) => a.live.weight - b.live.weight)
    }

    function moveUp(component: PageBuilderItem) {
        const siblings = pageBuilderItems.value.filter(obj => obj.live.parent === component.live.parent)

        const index = siblings.findIndex(obj => obj.live.id === component.live.id)
        if (index > 0) {
            const globalIndex = pageBuilderItems.value.findIndex(obj => obj.live.id === component.live.id)
            const prevSiblingGlobalIndex = pageBuilderItems.value.findIndex(obj => obj.live.id === siblings[index - 1].live.id)

            ;[pageBuilderItems.value[globalIndex], pageBuilderItems.value[prevSiblingGlobalIndex]] =
                [pageBuilderItems.value[prevSiblingGlobalIndex], pageBuilderItems.value[globalIndex]]

            ;[pageBuilderItems.value[globalIndex].live.weight, pageBuilderItems.value[prevSiblingGlobalIndex].live.weight] =
                [pageBuilderItems.value[prevSiblingGlobalIndex].live.weight, pageBuilderItems.value[globalIndex].live.weight]

            pageBuilderItems.value[globalIndex].rerender = true
            pageBuilderItems.value[prevSiblingGlobalIndex].rerender = true
        }
    }

    function moveDown(component: PageBuilderItem) {
        const siblings = pageBuilderItems.value.filter(obj => obj.live.parent === component.live.parent)

        const index = siblings.findIndex(obj => obj.live.id === component.live.id)
        if (index < siblings.length - 1) {
            const globalIndex = pageBuilderItems.value.findIndex(obj => obj.live.id === component.live.id)
            const nextSiblingGlobalIndex = pageBuilderItems.value.findIndex(obj => obj.live.id === siblings[index + 1].live.id)

            ;[pageBuilderItems.value[globalIndex], pageBuilderItems.value[nextSiblingGlobalIndex]] =
                [pageBuilderItems.value[nextSiblingGlobalIndex], pageBuilderItems.value[globalIndex]]

            ;[pageBuilderItems.value[globalIndex].live.weight, pageBuilderItems.value[nextSiblingGlobalIndex].live.weight] =
                [pageBuilderItems.value[nextSiblingGlobalIndex].live.weight, pageBuilderItems.value[globalIndex].live.weight]

            pageBuilderItems.value[globalIndex].rerender = true
            pageBuilderItems.value[nextSiblingGlobalIndex].rerender = true
        }
    }

    function remove(component: PageBuilderItem) {
        const index = pageBuilderItems.value.findIndex(obj => obj.live.id === component.live.id)
        if (index !== -1) {
            removeChildren(component.live.id)
            pageBuilderItems.value.splice(index, 1)
        }
    }

    function removeChildren(parentId: string) {
        const children = pageBuilderItems.value.filter(obj => obj.live.parent === parentId)

        children.forEach(child => {
            removeChildren(child.live.id)
            const childIndex = pageBuilderItems.value.findIndex(obj => obj.live.id === child.live.id)
            if (childIndex !== -1) {
                pageBuilderItems.value.splice(childIndex, 1)
            }
        })
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

        data.weight = pageBuilderItems.value.length + 1
        data.parent = null
        if (currentComponent.value !== null) {
            data.parent = currentComponent.value.live.id
            currentComponent.value.rerender = true
        }

        data.container = false
        if (components.value[id].container === true) {
            data.container = true
        }

        const pageBuilderItem = {
            isNew: true,
            live: clone(data as PageComponent),
            original: clone(data as PageComponent),
            rerender: false,
            isDirty() {
                return this.isNew || !equal(this.live, this.original)
            },
            children() {
                return getChildren(this)
            }
        }

        if (pageBuilderItem.live.container) {
            setCurrentComponent(pageBuilderItem)
        }

        pageBuilderItems.value.push(pageBuilderItem as PageBuilderItem)
    }

    function updateEditors(components: PageBuilderItem[]) {
        components.forEach((component, index) => {
            component.live.weight = index + 1
            return component
        })
    }

    function setCurrentComponent(pageBuilderItem: PageBuilderItem | null) {
        currentComponent.value = pageBuilderItem
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
        setCurrentComponent,
        rootItems,
        getChildren,
        setCurrentPageUuid,
        allItems,
        currentComponent,
        currentPageUuid,
        update,
        remove,
        undo,
        updateEditors,
        moveUp,
        editComponent,
        moveDown,
        currentEdit,
        initialize
    }
})
