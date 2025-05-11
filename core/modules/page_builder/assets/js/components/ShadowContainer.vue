<template>
    <div ref="host"/>
</template>

<script lang="ts" setup>
import {createApp, onMounted, ref} from 'vue';
import NestedComponent from '@modules/page_builder/assets/js/components/NestedComponent.vue';

const host = ref<HTMLElement | null>(null);

const props = defineProps<{
    modelValue: any[];
}>();

const emit = defineEmits(['update:modelValue']);

onMounted(() => {
    if (host.value) {
        const shadowRoot = host.value.attachShadow({mode: 'open'});
        const mountPoint = document.createElement('div');
        shadowRoot.appendChild(mountPoint);

        const shadowApp = createApp(NestedComponent, {
            modelValue: props.modelValue,
            'onUpdate:modelValue': (val: any[]) => emit('update:modelValue', val),
        });

        shadowApp.mount(mountPoint);
    }
});

</script>
