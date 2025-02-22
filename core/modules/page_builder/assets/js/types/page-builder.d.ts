export type ComponentType = {
    id: string
    name: string
    version: string
    template: string
    settings: ComponentTypeSettings
    fields: Record<string, Field>
}

export type Page = {
    title: string
    slug: string
    components: Component[]
}

export type ComponentSettings = {
    position?: string | null | undefined
    parent?: string | null | undefined
    locked?: boolean | null | undefined
}

export type ComponentTypeSettings = {
    root: boolean
    container: boolean
    placeholder: boolean
    hidden: boolean
    css: object
    default_position: string | null
    positions: Record<string, Position>
}

export type Position = {
    label: string
    conditions: PositionCondition[]
}

export type PositionCondition = {
    field: string
    value: any
}

export type Target = {
    component: Component
    position: string | null
}

export type ComponentField = {
    id: string
    field_name: string
    field_type: string
    value_type: string
    int_value: number
    varchar_value: string
    text_value: string
    json_value: JSON
    blob_value: any
    value: any
}

export type Component = {
    id: string
    original: Component
    weight?: number
    parent: string | null
    component_type: string
    rendered_html: string
    component_fields: ComponentField[]
    locked: boolean | number
    position: string | null
    is_new: boolean
    children: Component[]
}

export type Field = {
    type: string
    label: string
    default_value: string
    value?: string
    settings?: Record<string, any>
}
