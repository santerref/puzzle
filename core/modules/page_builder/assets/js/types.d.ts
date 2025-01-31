export type ComponentType = {
    fields: any
    group: string
    id: string
    name: string
    root: boolean
    template: string
    html: string
}

export type PageBuilderItem = {
    isNew: boolean
    live: PageComponent
    original: PageComponent
    rerender: boolean
    isDirty: () => boolean
    children: () => PageBuilderItem[]
}

export type PageComponent = {
    id: string
    component_type: string
    rendered_html: string
    form_values: object
    weight: number
    container: boolean
    parent: string | null
}

export type Field = {
    default_value: string
    value: string
    type: string
    label: string
    options?: object
}

declare global {
    interface Window {
        page_uuid: string
    }
}
