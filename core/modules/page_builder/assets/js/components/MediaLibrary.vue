<template>
    <div>
        <slot :open="open"/>
        <div v-if="showMediaSelector">
            <div
                class="fixed w-full max-w-screen-lg max-h-full overflow-auto bg-white shadow-lg z-30 p-8 left-1/2 top-8 -translate-x-1/2 bottom-8"
            >
                <h2 class="text-xl font-bold border-b border-stone-200">
                    Media
                </h2>
                <div class="my-4">
                    <div class="flex items-center space-x-2">
                        <label
                            for="file"
                            class="cursor-pointer bg-stone-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-stone-700"
                        >
                            <!-- @TODO: If current file, set to cancel and cancel the upload. -->
                            Choose File
                        </label>
                        <input
                            v-if="imageOnly"
                            id="file"
                            ref="file"
                            type="file"
                            accept=".png, .jpg, .jpeg, .gif"
                            class="hidden"
                            @change="uploadFile"
                        >
                        <input
                            v-else
                            id="file"
                            ref="file"
                            type="file"
                            class="hidden"
                            @change="uploadFile"
                        >
                        <span
                            v-if="!currentFile"
                            id="file-name"
                            class="text-gray-700"
                        >
                            No file chosen
                        </span>
                        <span
                            v-else
                            id="file-name"
                            class="text-gray-700"
                        >
                            {{ currentFile.filename }}
                        </span>
                    </div>
                </div>

                <div class="my-4">
                    <div
                        v-if="isImage && currentFile"
                        class="cursor-crosshair relative"
                    >
                        <div
                            class="relative inline-flex"
                            @click="defineFocalPoint"
                        >
                            <img
                                ref="image"
                                :src="`https://puzzle.ddev.site${currentFile.path.replace('public/','')}`"
                                class="rounded-md max-h-[200px] max-w-[355px]"
                                height="200"
                                alt=""
                            >
                            <i
                                class="w-[20px] h-[20px] rounded-full bg-white/30 shadow border border-white/80 backdrop-invert backdrop-opacity-60 absolute -translate-x-1/2 -translate-y-1/2"
                                :style="{ top: `${focalPoint.y}%`, left: `${focalPoint.x}%` }"
                            />
                        </div>
                        <p class="text-sm text-stone-600 max-w-[400px]">
                            You can click on the image to define a focal point. By default, it's the center.
                        </p>
                    </div>
                    <div
                        v-if="currentFile && currentFile.is_image"
                        class="my-4"
                    >
                        <label class="font-medium mb-2 block">
                            Alternate text (alt attribute)
                        </label>
                        <div>
                            <input
                                v-model="alt"
                                class="border shadow w-full p-2 bg-white border-grey-400"
                                type="text"
                            >
                        </div>
                    </div>
                    <div
                        v-if="currentFile"
                        class="my-4"
                    >
                        <label class="font-medium mb-2 block">Title</label>
                        <div>
                            <input
                                v-model="title"
                                class="border shadow w-full p-2 bg-white border-grey-400"
                                type="text"
                            >
                        </div>
                        <p class="text-sm mt-1 text-stone-600 max-w-[400px]">
                            The title is an alternative to the file name when displaying a download link. It's also used
                            by the media library.
                        </p>
                    </div>
                </div>

                <button
                    v-if="currentFile"
                    class="cursor-pointer bg-stone-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-stone-700"
                    @click.prevent="save"
                >
                    Add
                </button>

                <div class="my-4">
                    <div
                        v-for="file in mediaStore.media"
                        :key="file.id"
                    >
                        <label :for="`media_${file.id}`">
                            <input
                                :id="`media_${file.id}`"
                                type="checkbox"
                                :checked="selectedMedia.includes(file.id)"
                                :disabled="isDisabled(file.id)"
                                @change="toggleSelection(file.id)"
                            >
                            <img
                                class="rounded-md"
                                :src="`/admin/files/${file.id}/50x50/${file.filename}`"
                                :alt="file.alt ?? ''"
                                width="50"
                                height="50"
                            >
                            {{ file.filename }}
                        </label>
                    </div>
                </div>

                <button
                    v-if="selectedMedia.length"
                    class="cursor-pointer bg-stone-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-stone-700"
                    @click.prevent="close"
                >
                    Select
                </button>
            </div>
            <div
                class="z-20 bg-black/20 fixed inset-0"
                @click.prevent="close"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import {computed, ref, useTemplateRef} from 'vue';
import {useMediaStore} from '@modules/page_builder/assets/js/stores/media';
import type {FocalPoint, StorageFile} from '@modules/page_builder/assets/js/types/media';

const mediaStore = useMediaStore();
mediaStore.loadMedia();

const emit = defineEmits<{
    (e: 'close', selectedMedia: string[]): void
}>();

const props = defineProps<{
    cardinality: number,
    imageOnly: boolean
}>();

const showMediaSelector = ref<boolean>(false);
const currentFile = ref<StorageFile | null>(null);
const focalPoint = ref<FocalPoint>({
    x: 50,
    y: 50
});
const title = ref<string>('');
const alt = ref<string>('');
const selectedMedia = ref<string[]>([]);

const open = () => {
    showMediaSelector.value = true;
};

const isImage = computed(() => currentFile.value && currentFile.value.is_image === true);
const isDisabled = computed(() => {
    return (id: string) => props.cardinality !== -1 && selectedMedia.value.length >= props.cardinality && !selectedMedia.value.includes(id);
});

const imageRef = useTemplateRef('image');
const fileRef = useTemplateRef('file');

const close = (): void => {
    emit('close', selectedMedia.value);
    selectedMedia.value = [];
    showMediaSelector.value = false;
};

const toggleSelection = (id: string) => {
    if (selectedMedia.value.includes(id)) {
        selectedMedia.value = selectedMedia.value.filter(selectedId => selectedId !== id);
    } else if (props.cardinality === -1 || selectedMedia.value.length < props.cardinality) {
        selectedMedia.value.push(id);
    }
};

const defineFocalPoint = (event: MouseEvent) => {
    if (!imageRef.value) return;

    const img = imageRef.value;
    const rect = img.getBoundingClientRect();

    const x = ((event.clientX - rect.left) / rect.width) * 100;
    const y = ((event.clientY - rect.top) / rect.height) * 100;

    focalPoint.value.x = x;
    focalPoint.value.y = y;
};

async function uploadFile(event: any): Promise<any> {
    if (event.target.files.length) {
        const file = event.target.files[0];
        const formData = new FormData();
        formData.append('upload_file', file);
        const response = await fetch('/admin/files/upload', {
            method: 'POST',
            body: formData,
        });

        const result = await response.json();
        currentFile.value = <StorageFile>result;
        if (fileRef.value) {
            (fileRef.value as HTMLInputElement).value = '';
        }
    }
}

async function save(): Promise<any> {
    if (currentFile.value) {
        let data = {
            title: title.value,
            alt: alt.value
        };
        if (currentFile.value.is_image) {
            data = Object.assign(data, {
                image: {
                    focal_point_x: focalPoint.value.x,
                    focal_point_y: focalPoint.value.y
                }
            });
        }

        const response = await fetch(`/admin/files/${currentFile.value.id}/save`, {
            method: 'POST',
            body: JSON.stringify(data),
        });

        mediaStore.media.unshift(await response.json() as StorageFile);

        focalPoint.value = {
            x: 50,
            y: 50
        };
        currentFile.value = null;
        title.value = '';
        alt.value = '';
    }
}
</script>
