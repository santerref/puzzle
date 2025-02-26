<template>
    <item-content
        v-if="component"
        :id="component.id"
        :key="component.id"
        :component="component"
        :component-count="componentCount"
    >
        <nested-component
            v-model="component.children"
            :class="cssClass"
        />
    </item-content>
</template>

<script setup lang="ts">
import type {Component} from '@modules/page_builder/assets/js/types/page-builder';
import {computed, onMounted, onUnmounted} from 'vue';
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';
import ItemContent from '@modules/page_builder/assets/js/components/ItemContent.vue';
import NestedComponent from '@modules/page_builder/assets/js/components/NestedComponent.vue';

const pageBuilder = usePageBuilderStore();
const props = withDefaults(defineProps<{
    componentUuid: string,
    position?: string | null,
    componentCount: number,
    cssClass?: string | null
}>(), {
    position: null,
    cssClass: null
});

const component = computed<Component>(() => {
    function findComponentByUUID(components: Component[], uuid: string): Component | null {
        for (const component of components) {
            if (component.id === props.componentUuid) {
                return component;
            }

            const foundInChildren = findComponentByUUID(component.children, uuid);
            if (foundInChildren) {
                return foundInChildren;
            }
        }

        return null;
    }

    return <Component>findComponentByUUID(pageBuilder.components, props.componentUuid);
});

onMounted(() => {
    pageBuilder.setMounted(props.componentUuid, true);
});

onUnmounted(() => {
    pageBuilder.setMounted(props.componentUuid, false);
});
</script>
