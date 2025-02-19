export type ComponentType = {
    name: string
    id: string
    version: string
    container: boolean
    root: boolean
    hidden: boolean
    has_fields: boolean
    placeholder: boolean
    settings: ComponentTypeSettings
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
    fields: Record<string, Field>
    template: string
    css?: object
    parents?: string[] | boolean
    default_position?: string
    positions?: Record<string, Position>
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

export type Component = {
    id: string
    original: Component
    weight?: number
    parent: string | null
    component_type: string
    rendered_html: string
    form_values: Record<string, any>
    locked: boolean | number
    position: string | null
    is_new: boolean
    children: Component[]
}

export type Field = {
    default_value: string
    value: string
    type: string
    label: string
    options?: object
}
