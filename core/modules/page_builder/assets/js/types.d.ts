export type ComponentType = {
    name: string
    id: string
    version: string
    container: boolean
    settings: ComponentTypeSettings
    group?: string
}

export type Page = {
    title: string
    slug: string
}

export type ComponentTypeSettings = {
    fields: object
    template: string
    css?: object
    parents?: string[] | boolean
    default_position?: string
    positions?: { [key: string], Position }
}

export type Position = {
    label: string
    conditions: PositionCondition[]
}

export type PositionCondition = {
    field: string
    value: any
}

export type PageBuilderItem = {
    isNew: boolean
    live: PageComponent
    original: PageComponent
    rerender: boolean
    isDirty: () => boolean
    children: (targetPosition: any) => PageBuilderItem[]
}

export type CurrentPosition = {
    uuid: any
    position: any
}

export type PageComponent = {
    id: string
    component_type: string
    rendered_html: string
    form_values: { [key: string], any }
    weight: number
    container: boolean
    parent: string | null
    position: string | null
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
