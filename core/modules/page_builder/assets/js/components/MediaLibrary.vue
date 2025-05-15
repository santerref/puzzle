<template>
    <div>
        <slot :open="open"/>
        <div v-if="showMediaSelector">
            <div
                class="tw:fixed tw:w-full tw:max-w-screen-lg tw:max-h-full tw:overflow-auto tw:bg-white tw:shadow-lg tw:z-30 tw:p-8 tw:left-1/2 tw:top-8 tw:-translate-x-1/2 tw:bottom-8"
            >
                <h2 class="tw:text-xl tw:font-bold tw:border-b tw:border-stone-200">
                    Media
                </h2>
                <div class="tw:my-4">
                    <div class="tw:flex tw:items-center tw:space-x-2">
                        <label
                            for="file"
                            class="tw:cursor-pointer tw:bg-stone-600 tw:text-white tw:px-4 tw:py-2 tw:rounded-lg tw:shadow-md tw:hover:bg-stone-700"
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
                            class="tw:hidden"
                            @change="uploadFile"
                        >
                        <input
                            v-else
                            id="file"
                            ref="file"
                            type="file"
                            class="tw:hidden"
                            @change="uploadFile"
                        >
                        <span
                            v-if="!currentFile"
                            id="file-name"
                            class="tw:text-gray-700"
                        >
                            No file chosen
                        </span>
                        <span
                            v-else
                            id="file-name"
                            class="tw:text-gray-700"
                        >
                            {{ currentFile.filename }}
                        </span>
                    </div>
                </div>

                <div class="tw:my-4">
                    <div
                        v-if="isImage && currentFile"
                        class="tw:cursor-crosshair tw:relative"
                    >
                        <div
                            class="tw:relative tw:inline-flex"
                            @click="defineFocalPoint"
                        >
                            <img
                                ref="image"
                                :src="`https://puzzle.ddev.site${currentFile.path.replace('public/','')}`"
                                class="tw:rounded-md tw:max-h-[200px] tw:max-w-[355px]"
                                height="200"
                                alt=""
                            >
                            <i
                                class="tw:w-[20px] tw:h-[20px] tw:rounded-full tw:bg-white/30 tw:shadow tw:border tw:border-white/80 tw:backdrop-invert tw:backdrop-opacity-60 tw:absolute tw:-translate-x-1/2 tw:-translate-y-1/2"
                                :style="{ top: `${focalPoint.y}%`, left: `${focalPoint.x}%` }"
                            />
                        </div>
                        <p class="tw:text-sm tw:text-stone-600 tw:max-w-[400px]">
                            You can click on the image to define a focal point. By default, it's the center.
                        </p>
                    </div>
                    <div
                        v-if="currentFile && currentFile.is_image"
                        class="tw:my-4"
                    >
                        <label class="tw:font-medium tw:mb-2 tw:block">
                            Alternate text (alt attribute)
                        </label>
                        <div>
                            <input
                                v-model="alt"
                                class="tw:border tw:shadow tw:w-full tw:p-2 tw:bg-white tw:border-grey-400"
                                type="text"
                            >
                        </div>
                    </div>
                    <div
                        v-if="currentFile"
                        class="tw:my-4"
                    >
                        <label class="tw:font-medium tw:mb-2 tw:block">Title</label>
                        <div>
                            <input
                                v-model="title"
                                class="tw:border tw:shadow tw:w-full tw:p-2 tw:bg-white tw:border-grey-400"
                                type="text"
                            >
                        </div>
                        <p class="tw:text-sm tw:mt-1 tw:text-stone-600 tw:max-w-[400px]">
                            The title is an alternative to the file name when displaying a download link. It's also used
                            by the media library.
                        </p>
                    </div>
                </div>

                <button
                    v-if="currentFile"
                    class="tw:cursor-pointer tw:bg-stone-600 tw:text-white tw:px-4 tw:py-2 tw:rounded-lg tw:shadow-md tw:hover:bg-stone-700"
                    @click.prevent="save"
                >
                    Add
                </button>

                <div class="tw:my-4">
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
                                class="tw:rounded-md"
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
                    class="tw:cursor-pointer tw:bg-stone-600 tw:text-white tw:px-4 tw:py-2 tw:rounded-lg tw:shadow-md tw:hover:bg-stone-700"
                    @click.prevent="close"
                >
                    Select
                </button>
            </div>
            <div
                class="tw:z-20 tw:bg-black/60 tw:fixed tw:inset-0"
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

const props = withDefaults(defineProps<{
    cardinality: number,
    imageOnly: boolean,
    selected?: string[]
}>(),{
    selected: () => [] as string[]
});

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
    selectedMedia.value.push(...props.selected);
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
            headers: {
                'X-Puzzle-Csrf-Token': window.csrfToken
            }
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
            headers: {
                'X-Puzzle-Csrf-Token': window.csrfToken
            }
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
