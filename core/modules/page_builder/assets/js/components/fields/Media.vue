<template>
    <media-library
        v-slot="{open}"
        :cardinality="cardinality"
        :image-only="imageOnly"
        @close="setSelectedMedia"
    >
        <label class="font-medium mb-2 block">Media</label>
        <button
            class="border-2 border-stone-300 text-stone-600 px-4 cursor-pointer py-2 text-sm uppercase font-bold"
            @click.prevent="open"
        >
            Select media
        </button>
    </media-library>
    <div
        v-if="media.length"
        class="space-y-4"
    >
        <img
            v-for="image in media"
            :key="image.id"
            class="rounded-md"
            :src="`/admin/files/${image.id}/355x200/${image.filename}`"
            width="355"
            height="200"
            alt=""
        >
    </div>
</template>

<script setup lang="ts">
import MediaLibrary from '@modules/page_builder/assets/js/components/MediaLibrary.vue';
import {ComponentField} from '@modules/page_builder/assets/js/types/page-builder';
import {computed, ref} from 'vue';
import {get} from 'lodash';
import {useMediaStore} from '@modules/page_builder/assets/js/stores/media';

const model = defineModel<string>();
const mediaStore = useMediaStore();
const props = defineProps<{
    field: ComponentField
}>();

const emit = defineEmits<{
    (e: 'update:modelValue')
}>();

const setSelectedMedia = (value: string[]) => {
    emit('update:modelValue', value.join(','));
    selectedMedia.value = value;
};

const selectedMedia = ref<string[]>(model.value ? model.value.split(',') : []);
const media = computed(() => mediaStore.media.filter(media => selectedMedia.value.includes(media.id)));

const imageOnly = computed<boolean>(() => Boolean(get(props.field, 'options.image_only', false)));
const cardinality = computed<number>(() => Number(get(props.field, 'options.cardinality', -1)));
</script>
