import {defineStore} from 'pinia';
import {ref} from 'vue';
import {StorageFile} from '@modules/page_builder/assets/js/types/media';

export const useMediaStore = defineStore('media', () => {
    const isLoading = ref<boolean>(true);
    const media = ref<StorageFile[]>([]);

    async function loadMedia(): Promise<void> {
        isLoading.value = true;
        const response = await fetch('/admin/files');
        media.value = await response.json() as StorageFile[];
        isLoading.value = false;
    }

    return {
        isLoading,
        loadMedia,
        media
    };
});
