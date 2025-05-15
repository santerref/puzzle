<template>
    <media-library
        v-slot="{open}"
        :cardinality="cardinality"
        :image-only="imageOnly"
        :selected="selected"
        @close="setSelectedMedia"
    >
        <label class="tw:font-medium tw:mb-2 tw:block">Media</label>
        <button
            class="tw:border-2 tw:border-stone-300 tw:text-stone-600 tw:px-4 tw:cursor-pointer tw:py-2 tw:text-sm tw:uppercase tw:font-bold"
            @click.prevent="open"
        >
            Select media
        </button>
    </media-library>
    <div
        v-if="media.length"
        class="tw:space-y-4"
    >
        <img
            v-for="image in media"
            :key="image.id"
            class="tw:rounded-md"
            :src="`/admin/files/${image.id}/355x200/${image.filename}`"
            width="355"
            height="200"
            alt=""
        >
    </div>
</template>

<script setup lang="ts">
import MediaLibrary from '@modules/page_builder/assets/js/components/MediaLibrary.vue';
import {ComponentField, type ComponentType, Field} from '@modules/page_builder/assets/js/types/page-builder';
import {computed, ref} from 'vue';
import {get} from 'lodash';
import {useMediaStore} from '@modules/page_builder/assets/js/stores/media';

const model = defineModel<object[]>();
const mediaStore = useMediaStore();
const props = defineProps<{
    field: ComponentField,
    componentType: ComponentType
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: any[]): void
}>();

const setSelectedMedia = (value: string[]) => {
    const media = value.map((uuid) => {
        return {
            id: uuid
        };
    });
    emit('update:modelValue', media);
    selectedMedia.value = media;
};

const selectedMedia = ref<any[]>(model.value ?? []);
const media = computed(() => mediaStore.media.filter(media => selectedMedia.value.find(selected => selected.id === media.id)));
const selected = computed(() => selectedMedia.value.map(item => item.id));

const fieldType = computed(() => <Field>props.componentType.fields.find((field) => field.id === props.field.field_name));
const settings = computed(() => fieldType.value.settings);

const imageOnly = computed<boolean>(() => Boolean(get(settings.value, 'image_only', false)));
const cardinality = computed<number>(() => Number(get(settings.value, 'cardinality', -1)));
</script>
