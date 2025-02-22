<template>
    <div class="fixed z-30 top-0 right-0 bottom-0 w-[700px] shadow-md p-5 bg-stone-50 max-h-full overflow-auto">
        <div class="flex flex-col min-h-full">
            <div class="flex-grow">
                <h1 class="text-lg mb-4">
                    Edit <strong>{{ componentType.name }}</strong>
                </h1>
                <div class="space-y-4">
                    <component
                        :is="pascalCase(field.field_type)"
                        v-for="field in model.component_fields"
                        :key="field.id"
                        v-model="field[field.value_type+'_value']"
                        :field="field"
                    />
                </div>
            </div>
            <div class="flex gap-4 mt-4 flex-shrink-0">
                <button
                    class="bg-black cursor-pointer text-white px-4 py-2 font-bold"
                    @click.prevent="save"
                >
                    Save
                </button>
                <button
                    class="bg-gray-300 cursor-pointer px-4 py-2 font-bold"
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

const model = defineModel<Component>({required: true});

const pageBuilder = usePageBuilderStore();
const componentType = computed(() => pageBuilder.getComponentType(model.value.component_type));

const save = async function () {
    const response = await fetch(`/api/components/${model.value.component_type}/refresh`, {
        method: 'PUT',
        body: JSON.stringify({
            component_fields: model.value.component_fields,
            uuid: model.value.id
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    });
    Object.assign(model.value, await response.json());
    pageBuilder.closeSettings();
};
</script>
