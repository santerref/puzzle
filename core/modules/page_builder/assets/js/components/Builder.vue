<template>
    <template v-if="!pageBuilder.loading">
        <div class="bg-stone-100 w-full p-3 flex gap-4 justify-end">
            <a
                class="text-white bg-stone-600 px-4 py-2 font-medium"
                target="_blank"
                :href="`/${pageBuilder.page.slug}`"
            >
                View
            </a>
            <button
                :class="{
                    'cursor-not-allowed opacity-50': !pageBuilder.saveRequired,
                    'cursor-pointer': pageBuilder.saveRequired,
                }"
                :disabled="!pageBuilder.saveRequired"
                class="text-white bg-stone-600 px-4 py-2 font-medium"
                @click.prevent="pageBuilder.save"
            >
                Save
            </button>
        </div>
        <div class="grid grid-cols-12">
            <div class="col-span-3 px-4 pb-4 bg-stone-100">
                <div class="mb-4 bg-white p-5 ">
                    <p class="font-semibold mb-2">
                        Components Tree
                    </p>
                    <ul class="mt-2">
                        <li>
                            <button
                                class="cursor-pointer text-blue-500 underline"
                                :class="{'bg-stone-200':!pageBuilder.hasTarget()}"
                                @click.prevent="pageBuilder.unsetTarget()"
                            >
                                Root
                            </button>
                            <ul class="pl-2">
                                <TreeItem
                                    v-for="component in sortedComponents"
                                    :key="component.id"
                                    :component="component"
                                />
                            </ul>
                        </li>
                    </ul>
                </div>

                <ul class="grid grid-cols-2 gap-4">
                    <li
                        v-for="componentType in pageBuilder.availableComponentTypes"
                        :key="componentType.id"
                    >
                        <button
                            class=" bg-white border border-stone-100 text-stone-600 text-sm font-medium uppercase rounded shadow block w-full py-2 px-4 cursor-pointer min-h-[100px]"
                            @click.prevent="pageBuilder.createComponent(componentType.id)"
                        >
                            {{ componentType.name }}
                        </button>
                    </li>
                </ul>
            </div>
            <div class="col-span-9">
                <div class="container m-auto">
                    <div>
                        <nested-component
                            v-if="pageBuilder.components.length"
                            v-model="pageBuilder.components"
                        />
                        <div
                            v-else
                            class="flex items-center py-8"
                        >
                            <p class="text-center px-6 py-4 rounded text-stone-600 font-medium m-auto d-block bg-stone-100">
                                Add a section to start building your page
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <editor
            v-if="pageBuilder.currentComponentSettings !== null"
            v-model="pageBuilder.currentComponentSettings"
        />
    </template>
</template>

<script setup lang="ts">
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';
import NestedComponent from '@modules/page_builder/assets/js/components/NestedComponent.vue';
import TreeItem from '@modules/page_builder/assets/js/components/TreeItem.vue';
import Editor from '@modules/page_builder/assets/js/components/Editor.vue';
import {computed} from 'vue';
import {sortBy} from 'lodash';

const pageBuilder = usePageBuilderStore();
pageBuilder.initialize();

const sortedComponents = computed(() => sortBy(pageBuilder.components, 'weight'));
</script>
