export type Component = {
    fields: object
    group: string
    id: string
    name: string
    root: boolean
    template: string
    html: string
}

export type EditorComponent = {
    id: string
    original: Component
    user: Component
    isDirty: boolean
    weight: number
    originalWeight: number
}

export type Field = {
    default_value: string
    value: string
    type: string
    label: string
    options: ?object
}
