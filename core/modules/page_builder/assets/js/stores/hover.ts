import {defineStore} from 'pinia';
import {computed, ref} from 'vue';

export const useHoverStore = defineStore('hover', () => {
    const hoverStack = ref<string[]>([]);
    const locked = ref<boolean>(false);

    const pushHover = (componentId: string) => {
        if(locked.value) {
            return;
        }

        if (!hoverStack.value.includes(componentId)) {
            hoverStack.value.push(componentId);
        }
    };

    const popHover = (componentId: string) => {
        if(locked.value) {
            return;
        }

        const idx = hoverStack.value.lastIndexOf(componentId);
        if (idx !== -1) {
            hoverStack.value.splice(idx, 1);
        }
    };

    const lock = () => locked.value = true;
    const unlock = () => locked.value = false;

    const currentHover = computed(() => {
        return hoverStack.value[hoverStack.value.length - 1] || null;
    });

    return {
        pushHover,
        hoverStack,
        popHover,
        currentHover,
        lock,
        unlock
    };
});
