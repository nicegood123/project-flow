<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    status: string;
}>();

const statuses: Record<
    string,
    { dot: string; label: string; surface: string }
> = {
    Planning: {
        dot: 'bg-[#547991]',
        label: 'text-[#28536f]',
        surface: 'border-[#a7c0cd] bg-[#eef6f9]',
    },
    'In Progress': {
        dot: 'bg-[#bc7c17]',
        label: 'text-[#7a4b09]',
        surface: 'border-[#dfc58f] bg-[#fff8e7]',
    },
    'On Hold': {
        dot: 'bg-[#8a6475]',
        label: 'text-[#694153]',
        surface: 'border-[#d0b8c2] bg-[#fbf1f4]',
    },
    Completed: {
        dot: 'bg-[#46835d]',
        label: 'text-[#28563a]',
        surface: 'border-[#9ab7a4] bg-[#eef7ef]',
    },
};

const indicator = computed(
    () =>
        statuses[props.status] ?? {
            dot: 'bg-[#6b7280]',
            label: 'text-[#4b5563]',
            surface: 'border-[#c4c8ce] bg-[#f5f6f7]',
        },
);
</script>

<template>
    <span
        :aria-label="`Status: ${status}`"
        class="inline-flex items-center gap-2 border px-2.5 py-1 text-xs font-semibold"
        :class="[indicator.surface, indicator.label]"
    >
        <span
            aria-hidden="true"
            class="size-1.5 rounded-full"
            :class="indicator.dot"
        />
        {{ status }}
    </span>
</template>
