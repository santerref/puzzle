<template>
<!--        <item
            v-for="pageBuilderItem in children"
            :key="pageBuilderItem.live.id"
            :component="pageBuilderItem"
        />-->
</template>

<script setup lang="ts">
/*import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/components'
import Item from '@modules/page_builder/assets/js/components/Item.vue'
import {VueDraggable} from 'vue-draggable-plus'
import {computed, onMounted} from 'vue'

const props = defineProps<{
    componentUuid?: string,
    position?: string,
    component?: string,
    locked?: boolean
}>()

const pageBuilder = usePageBuilderStore()

onMounted(() => {
    if (props.component) {
        if (children.value.length === 0 && props.position) {
            components.add(props.component, {
                position: props.position,
                parent: props.componentUuid,
                locked: props.locked ?? false
            })
        }
    }
})

const index = computed(() => components.allItems.findIndex(obj => obj.live.id === props.componentUuid))
const children = computed(() => {
    if (index.value !== -1) {
        return components.getChildren(components.allItems[index.value], props.position)
    }
    return []
})*/
</script>
