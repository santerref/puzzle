<template>
    <li>
        <button
            v-if="container"
            class="cursor-pointer text-blue-500 underline"
            :class="{'bg-stone-200':active && (innerPosition === null || Object.keys(positions).length === 1)}"
            @click.prevent="updateInnerPosition(componentPosition)"
        >
            {{ components.components[pageBuilderItem.live.component_type].name }}
        </button>
        <p v-else>
            {{ components.components[pageBuilderItem.live.component_type].name }}
        </p>
        <ul
            v-if="container"
            class="pl-4 list-disc"
        >
            <template v-if="positionsCount <= 1">
                <TreeItem
                    v-for="child in children(innerPosition)"
                    :key="child.live.id"
                    :page-builder-item="child"
                />
            </template>
            <li
                v-for="(position, key) in positions"
                v-else
                :key="key"
            >
                <button
                    class="cursor-pointer text-blue-500 underline"
                    :class="{'bg-stone-200':active && key === innerPosition}"
                    @click.prevent="updateInnerPosition(key)"
                >
                    {{ position.label }}
                </button>
                <ul class="pl-4 list-disc">
                    <TreeItem
                        v-for="child in children(key)"
                        :key="child.live.id"
                        :page-builder-item="child"
                    />
                </ul>
            </li>
        </ul>
    </li>
</template>

<script setup lang="ts">
import {PageBuilderItem, Position} from '@modules/page_builder/assets/js/types'
import {useComponentsStore} from '@modules/page_builder/assets/js/stores/components'
import {computed, ref} from 'vue'
import cloneDeep from 'clone-deep'

const components = useComponentsStore()

const props = defineProps<{
    pageBuilderItem: PageBuilderItem
}>()

const componentPosition = computed(() => {
    const id = props.pageBuilderItem.live.component_type
    if (components.components[id].settings.positions && Object.keys(components.components[id].settings.positions).length > 0) {
        return components.components[id].settings.default_position
    }
    return null
})
const innerPosition = ref<string | null>(componentPosition.value)

function updateInnerPosition(key) {
    components.setCurrentComponent(props.pageBuilderItem, key)
    innerPosition.value = key
}

const container = computed(() => {
    const id = props.pageBuilderItem.live.component_type
    return components.components[id].container ?? false
})

const positions = computed(() => {
    const id = props.pageBuilderItem.live.component_type
    if (components.components[id].settings.positions) {
        const componentPositions = cloneDeep(components.components[id].settings.positions)
        for (const key in componentPositions) {
            if (Object.prototype.hasOwnProperty.call(componentPositions, key)) {
                const position: Position = componentPositions[key]
                if (position.conditions) {
                    position.conditions.forEach((condition) => {
                        const fieldValue = props.pageBuilderItem.live.form_values[condition.field].toString()
                        let valid
                        if (Array.isArray(condition.value)) {
                            valid = condition.value.includes(fieldValue)
                        } else {
                            valid = fieldValue === condition.value
                        }

                        if (!valid) {
                            delete componentPositions[key]
                        }
                    })
                }
            }
        }
        return componentPositions
    }
    return {}
})
const positionsCount = computed(() => {
    return Object.keys(positions.value).length
})

const active = computed(() => {
    return components.currentComponent &&
        components.currentComponent.live.id === props.pageBuilderItem.live.id
})

const children = function (position: any) {
    return props.pageBuilderItem.children(position)
}
</script>
