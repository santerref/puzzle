<template>
    <div
        v-if="contextual.menuVisible"
        class="tw:fixed tw:bg-indigo-500 tw:text-white tw:border-indigo-700 tw:rounded tw:shadow-lg tw:min-w-[160px] tw:p-1 tw:z-50"
        :style="{ top: `${contextual.menuPosition.y}px`, left: `${contextual.menuPosition.x}px` }"
    >
        <ul class="tw:flex tw:flex-col tw:gap-2 tw:pointer">
            <li
                class="tw:hover:bg-indigo-400 tw:px-2 tw:py-1 tw:text-sm tw:cursor-pointer"
                @click.stop="handleAction('edit')"
            >
                Edit
            </li>
            <li
                v-if="!contextual.currentComponent?.locked"
                class="tw:hover:bg-indigo-400 tw:px-2 tw:py-1 tw:text-sm tw:cursor-pointer"
                @click.stop="handleAction('delete')"
            >
                Delete
            </li>
        </ul>
    </div>
</template>

<script lang="ts" setup>
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';
import {useContextualStore} from '@modules/page_builder/assets/js/stores/contextual';
import {Component} from '@modules/page_builder/assets/js/types/page-builder';

const pageBuilder = usePageBuilderStore();
const contextual = useContextualStore();

function handleAction(action: string) {
    const component = contextual.currentComponent as Component;
    if (action === 'edit') {
        pageBuilder.openSettings(component);
    }
    if (action === 'delete' && !component.locked) {
        pageBuilder.removeComponent(component);
    }
    contextual.closeMenu();
}

document.addEventListener('click', () => {
    if (contextual.menuVisible) {
        contextual.closeMenu();
    }
});
</script>
