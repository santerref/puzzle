<template>
    <li v-if="mounted">
        <div class="tw:flex tw:justify-between tw:py-0.5 tw:px-2 tw:hover:bg-stone-100">
            <div>
                <button
                    v-if="componentType.settings.container"
                    :class="{'tw:bg-stone-200':pageBuilder.currentTargetIs(component)}"
                    class="tw:cursor-pointer tw:text-indigo-500 tw:underline"
                    @click.prevent="pageBuilder.setTarget(component, component.position)"
                >
                    {{ componentType.name }}
                </button>
                <p v-else>
                    {{ componentType.name }}
                </p>
            </div>
            <div>
                <i
                    v-if="hasFields"
                    class="pi pi-cog tw:hover:cursor-pointer"
                    @click.prevent="pageBuilder.openSettings(component)"
                />
            </div>
        </div>
        <ul
            class="tw:pl-2"
        >
            <TreeItem
                v-for="child in component.children"
                :key="child.id"
                :component="child"
            />
        </ul>
    </li>
</template>

<script setup lang="ts">
import type {Component} from '@modules/page_builder/assets/js/types/page-builder';
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';
import {computed} from 'vue';
import {isEmpty} from 'lodash';

const props = defineProps<{
    component: Component
}>();

const pageBuilder = usePageBuilderStore();
const componentType = computed(() => pageBuilder.getComponentType(props.component.component_type));
const hasFields = computed(() => !isEmpty(componentType.value.fields));
const mounted = computed(() => {
    return pageBuilder.isMounted(props.component.id);
});
</script>
