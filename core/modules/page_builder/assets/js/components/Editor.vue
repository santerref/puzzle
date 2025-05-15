<template>
    <div class="tw:fixed tw:z-30 tw:top-0 tw:right-0 tw:bottom-0 tw:w-[450px] tw:shadow-lg tw:p-5 tw:bg-stone-50 tw:max-h-full tw:overflow-auto">
        <div class="tw:flex tw:flex-col tw:min-h-full">
            <div class="tw:flex-grow">
                <h1 class="tw:text-lg tw:mb-4">
                    Edit <strong>{{ componentType.name }}</strong>
                </h1>
                <div class="tw:space-y-4">
                    <component
                        :is="pascalCase(field.field_type)"
                        v-for="field in sortedFields"
                        :key="field.id"
                        v-model="field[field.value_type + '_value']"
                        :field="field"
                        :component-type="componentType"
                    />
                </div>
            </div>
            <div class="tw:flex tw:gap-4 tw:mt-4 tw:flex-shrink-0">
                <button
                    class="tw:bg-black tw:cursor-pointer tw:text-white tw:px-4 tw:py-2 tw:font-bold"
                    @click.prevent="save"
                >
                    Save
                </button>
                <button
                    class="tw:bg-gray-300 tw:cursor-pointer tw:px-4 tw:py-2 tw:font-bold"
                    @click.prevent="pageBuilder.closeSettings()"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';
import type {Component} from '@modules/page_builder/assets/js/types/page-builder';
import {computed} from 'vue';
import {pascalCase} from 'change-case';
import {sortBy} from 'lodash';
import cloneDeep from 'clone-deep';

const model = defineModel<Component>({required: true});
const clonedModel = cloneDeep(model.value);

const pageBuilder = usePageBuilderStore();
const componentType = computed(() => pageBuilder.getComponentType(clonedModel.component_type));

const sortedFields = computed(() => sortBy(clonedModel.component_fields, 'weight'));

const save = async function () {
    const response = await fetch(`/api/components/${clonedModel.component_type}/render`, {
        method: 'PUT',
        body: JSON.stringify({
            component_fields: clonedModel.component_fields,
            uuid: clonedModel.id
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-Puzzle-Page-Uuid': pageBuilder.page.id,
            'X-Puzzle-Csrf-Token': window.csrfToken
        }
    });
    Object.assign(model.value, Object.fromEntries(Object.entries(await response.json()).filter(([key]) => key !== 'children')));
    pageBuilder.closeSettings();
};
</script>
