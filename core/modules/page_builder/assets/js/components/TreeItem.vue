<template>
    <li>
        <div class="flex justify-between py-0.5 px-2 hover:bg-stone-100">
            <div>
                <button
                    v-if="componentType.container"
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
                    v-if="componentType.has_fields"
                    class="pi pi-cog hover:cursor-pointer"
                    @click.prevent="pageBuilder.openSettings(component)"
                />
            </div>
        </div>
        <ul
            class="pl-2"
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

const props = defineProps<{
    component: Component
}>();

const pageBuilder = usePageBuilderStore();
const componentType = computed(() => pageBuilder.getComponentType(props.component.component_type));
</script>
