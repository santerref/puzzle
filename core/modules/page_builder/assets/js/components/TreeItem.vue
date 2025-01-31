<template>
    <li>
        <button
            v-if="pageBuilderItem.live.container"
            class="cursor-pointer text-blue-500 underline"
            :class="{'bg-stone-200':active}"
            @click.prevent="components.setCurrentComponent(pageBuilderItem)"
        >
            {{ components.components[pageBuilderItem.live.component_type].name }}
        </button>
        <p v-else>
            {{ components.components[pageBuilderItem.live.component_type].name }}
        </p>
        <ul
            v-if="children.length > 0"
            class="pl-4 list-disc"
        >
            <TreeItem
                v-for="child in children"
                :key="child.live.id"
                :page-builder-item="child"
            />
        </ul>
    </li>
</template>

<script setup lang="ts">
import {PageBuilderItem} from '@modules/page_builder/assets/js/types'
import {useComponentsStore} from '@modules/page_builder/assets/js/stores/components'
import {computed} from 'vue'

const components = useComponentsStore()

const props = defineProps<{
    pageBuilderItem: PageBuilderItem
}>()

const active = computed(() => components.currentComponent && components.currentComponent.live.id === props.pageBuilderItem.live.id)

const children = computed(() => props.pageBuilderItem.children())
</script>
