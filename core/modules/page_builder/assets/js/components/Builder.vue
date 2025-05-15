<template>
    <template v-if="!pageBuilder.loading">
        <div class="tw:w-full tw:p-3 tw:flex tw:gap-4 tw:justify-end">
            <a
                class="tw:text-white tw:bg-stone-600 tw:px-4 tw:py-2 tw:font-medium"
                target="_blank"
                :href="`/${pageBuilder.page.slug}`"
            >
                View
            </a>
            <button
                :class="{
                    'tw:cursor-not-allowed tw:opacity-50': !pageBuilder.saveRequired,
                    'tw:cursor-pointer': pageBuilder.saveRequired,
                }"
                :disabled="!pageBuilder.saveRequired"
                class="tw:text-white tw:bg-stone-600 tw:px-4 tw:py-2 tw:font-medium"
                @click.prevent="pageBuilder.save"
            >
                Save
            </button>
        </div>
        <div class="tw:grid tw:grid-cols-12">
            <div class="tw:col-span-3">
                <div
                    class="tw:mx-4 tw:shadow-lg tw:rounded tw:px-4 tw:py-6 tw:max-w-full tw:w-[400px] tw:border tw:border-stone-200"
                >
                    <div class="tw:mb-4 tw:bg-white tw:px-5">
                        <p class="tw:font-semibold tw:mb-2">
                            Components Tree
                        </p>
                        <ul class="tw:mt-2 tw:text-sm">
                            <li>
                                <button
                                    class="tw:cursor-pointer tw:text-indigo-500 tw:underline"
                                    :class="{'tw:bg-stone-200':!pageBuilder.hasTarget()}"
                                    @click.prevent="pageBuilder.unsetTarget()"
                                >
                                    Root
                                </button>
                                <ul class="tw:pl-2">
                                    <TreeItem
                                        v-for="component in pageBuilder.components"
                                        :key="component.id"
                                        :component="component"
                                    />
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <ul class="tw:grid tw:grid-cols-2 tw:gap-4">
                        <li
                            v-for="componentType in pageBuilder.availableComponentTypes"
                            :key="componentType.id"
                        >
                            <button
                                class="tw:bg-white tw:border tw:border-stone-100 tw:text-stone-600 tw:text-sm tw:font-medium tw:uppercase tw:rounded tw:shadow tw:block tw:w-full tw:py-2 tw:px-4 tw:cursor-pointer tw:min-h-[100px]"
                                @click.prevent="pageBuilder.createComponent(componentType.id)"
                            >
                                {{ componentType.name }}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tw:col-span-9">
                <div class="tw:container tw:m-auto">
                    <div>
                        <shadow-container
                            v-if="pageBuilder.components.length"
                            v-model="pageBuilder.components"
                        />
                        <div
                            v-else
                            class="tw:flex tw:items-center tw:py-8"
                        >
                            <p class="tw:text-center tw:px-6 tw:py-4 tw:rounded tw:text-stone-600 tw:font-medium tw:m-auto tw:d-block tw:bg-stone-100">
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
        <contextual-menu/>
        <hover-outline/>
    </template>
</template>

<script setup lang="ts">
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';
import TreeItem from '@modules/page_builder/assets/js/components/TreeItem.vue';
import Editor from '@modules/page_builder/assets/js/components/Editor.vue';
import ContextualMenu from '@modules/page_builder/assets/js/components/ContextualMenu.vue';
import ShadowContainer from '@modules/page_builder/assets/js/components/ShadowContainer.vue';
import HoverOutline from '@modules/page_builder/assets/js/components/HoverOutline.vue';

const pageBuilder = usePageBuilderStore();
pageBuilder.initialize();
</script>
