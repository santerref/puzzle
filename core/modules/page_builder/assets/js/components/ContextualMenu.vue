<template>
    <div
        v-if="contextual.menuVisible"
        class="fixed bg-indigo-500 text-white border-indigo-700 rounded shadow-lg min-w-[160px] p-1 z-50"
        :style="{ top: `${contextual.menuPosition.y}px`, left: `${contextual.menuPosition.x}px` }"
    >
        <ul class="flex flex-col gap-2 pointer">
            <li
                class="hover:bg-indigo-400 px-2 py-1 text-sm cursor-pointer"
                @click.stop="handleAction('edit')"
            >
                Edit
            </li>
            <li
                v-if="!contextual.currentComponent?.locked"
                class="hover:bg-indigo-400 px-2 py-1 text-sm cursor-pointer"
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
