<template>
    <VueDraggable
        v-if="children.length > 0"
        :model-value="children"
        handle=".handle"
        @update:model-value="components.updateEditors"
    >
        <item
            v-for="pageBuilderItem in children"
            :key="pageBuilderItem.live.id"
            :component="pageBuilderItem"
        />
    </VueDraggable>
</template>

<script setup lang="ts">
import {useComponentsStore} from '@modules/page_builder/assets/js/stores/components'
import Item from '@modules/page_builder/assets/js/components/Item.vue'
import {VueDraggable} from 'vue-draggable-plus'
import {computed} from 'vue'

const props = defineProps<{
    componentUuid?: string
}>()

const components = useComponentsStore()

const index = computed(() => components.allItems.findIndex(obj => obj.live.id === props.componentUuid))
const children = computed(() => {
    if (index.value !== -1) {
        return components.getChildren(components.allItems[index.value])
    }
    return []
})
</script>
