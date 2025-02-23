<template>
    <li v-if="mounted">
        <div class="flex justify-between py-0.5 px-2 hover:bg-stone-100">
            <div>
                <button
                    v-if="componentType.settings.container"
                    :class="{'bg-stone-200':pageBuilder.currentTargetIs(component)}"
                    class="cursor-pointer text-blue-500 underline"
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
                    class="pi pi-cog hover:cursor-pointer"
                    @click.prevent="pageBuilder.openSettings(component)"
                />
            </div>
        </div>
        <ul
            class="pl-2"
        >
            <TreeItem
                v-for="child in sortedChildren"
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
import {isEmpty, sortBy} from 'lodash';

const props = defineProps<{
    component: Component
}>();

const pageBuilder = usePageBuilderStore();
const componentType = computed(() => pageBuilder.getComponentType(props.component.component_type));
const hasFields = computed(() => !isEmpty(componentType.value.fields));
const sortedChildren = computed(() => sortBy(props.component.children, 'weight'));
const mounted = computed(() => {
    return pageBuilder.mountedComponents.hasOwnProperty(props.component.id) && pageBuilder.mountedComponents[props.component.id] === true;
});
</script>
