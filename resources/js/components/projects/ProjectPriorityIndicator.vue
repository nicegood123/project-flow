<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    priority: string;
}>();

const priorities: Record<string, { label: string; surface: string }> = {
    Low: {
        label: 'text-[#2f6a60]',
        surface: 'border-[#9fc7bd] bg-[#eff9f5]',
    },
    Medium: {
        label: 'text-[#86580b]',
        surface: 'border-[#e2c782] bg-[#fff8e7]',
    },
    High: {
        label: 'text-[#923823]',
        surface: 'border-[#e4afa1] bg-[#fff1ed]',
    },
};

const indicator = computed(
    () =>
        priorities[props.priority] ?? {
            label: 'text-[#4b5563]',
            surface: 'border-[#c4c8ce] bg-[#f5f6f7]',
        },
);
</script>

<template>
    <span
        :aria-label="`Priority: ${priority}`"
        class="inline-flex border px-2.5 py-1 text-xs font-semibold"
        :class="[indicator.surface, indicator.label]"
    >
        {{ priority }}
    </span>
</template>
