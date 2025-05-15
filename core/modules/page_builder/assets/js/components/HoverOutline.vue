<template>
    <Teleport to="body">
        <div
            v-if="targetEl && component"
            ref="outline"
            class="tw:fixed tw:border tw:z-40 tw:pointer-events-none tw:rounded"
            :class="{
                'tw:border-indigo-500': !componentType?.settings?.placeholder,
                'tw:border-indigo-500 tw:border-dashed': componentType?.settings?.placeholder
            }"
            :style="boxStyle"
        >
            <div
                v-if="componentType"
                class="tw:absolute tw:bg-indigo-500 tw:text-white tw:lowercase tw:font-bold tw:-translate-y-full tw:left-2 tw:rounded-tl tw:rounded-tr tw:text-xs tw:px-1 tw:py-0.5"
            >
                {{ componentType.name }}
            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import {useHoverStore} from '@modules/page_builder/assets/js/stores/hover';
import {usePageBuilderStore} from '@modules/page_builder/assets/js/stores/page-builder';
import {computed, onMounted, onUnmounted, ref, watch} from 'vue';

const hover = useHoverStore();
const pageBuilder = usePageBuilderStore();
const outline = ref<HTMLElement | null>(null);
const targetEl = ref<HTMLElement | null>(null);

const component = computed(() =>
    hover.currentHover ? pageBuilder.findComponentByUUID(pageBuilder.components, hover.currentHover) : null
);
const componentType = computed(() =>
    component.value ? pageBuilder.getComponentType(component.value.component_type) : null
);

const boxStyle = ref({top: '0px', left: '0px', width: '0px', height: '0px'});

function updateBox() {
    if (!component.value) return;

    const host = document.getElementById('shadow-root');
    let el: HTMLElement | null = null;

    if (host instanceof HTMLElement && host.shadowRoot) {
        el = host.shadowRoot.querySelector(`[data-uuid="${component.value.id}"]`);
    }

    if (el instanceof HTMLElement) {
        const rect = el.getBoundingClientRect();
        boxStyle.value = {
            top: `${rect.top}px`,
            left: `${rect.left}px`,
            width: `${rect.width}px`,
            height: `${rect.height}px`,
        };
        targetEl.value = el;
    } else {
        boxStyle.value = {top: '0px', left: '0px', width: '0px', height: '0px'};
        targetEl.value = null;
    }
}

watch(() => hover.currentHover, updateBox);
window.addEventListener('scroll', updateBox, true);
window.addEventListener('resize', updateBox, true);
onMounted(updateBox);
onUnmounted(() => {
    window.removeEventListener('scroll', updateBox, true);
    window.removeEventListener('resize', updateBox, true);
});
</script>
