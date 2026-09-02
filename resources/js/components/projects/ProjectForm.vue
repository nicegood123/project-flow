<script setup lang="ts">
import { reactive, ref } from 'vue';
import type { ProjectPayload } from '@/lib/projects';

const props = withDefaults(
    defineProps<{
        initialValues?: Partial<ProjectPayload>;
        isSubmitting?: boolean;
        serverErrors?: Record<string, string[]>;
        submitLabel?: string;
        submittingLabel?: string;
    }>(),
    {
        initialValues: () => ({}),
        isSubmitting: false,
        serverErrors: () => ({}),
        submitLabel: 'Create project',
        submittingLabel: 'Saving project',
    },
);

const emit = defineEmits<{
    submit: [project: ProjectPayload];
}>();

const statuses: ProjectPayload['status'][] = [
    'Planning',
    'In Progress',
    'On Hold',
    'Completed',
];
const priorities: ProjectPayload['priority'][] = ['Low', 'Medium', 'High'];
const errors = ref<Record<string, string>>({});
const form = reactive<ProjectPayload>({
    client_name: '',
    project_name: '',
    description: null,
    status: 'Planning',
    priority: 'Medium',
    start_date: null,
    due_date: null,
    ...props.initialValues,
});

function errorFor(field: string): string | undefined {
    return errors.value[field] ?? props.serverErrors[field]?.[0];
}

function submit(): void {
    const nextErrors: Record<string, string> = {};

    if (!form.client_name.trim()) {
        nextErrors.client_name = 'Client name is required.';
    }

    if (!form.project_name.trim()) {
        nextErrors.project_name = 'Project name is required.';
    }

    if (form.start_date && form.due_date && form.due_date < form.start_date) {
        nextErrors.due_date = 'Due date must be on or after the start date.';
    }

    errors.value = nextErrors;

    if (Object.keys(nextErrors).length === 0) {
        emit('submit', {
            ...form,
            client_name: form.client_name.trim(),
            project_name: form.project_name.trim(),
            description: form.description?.trim() || null,
        });
    }
}
</script>

<template>
    <form class="space-y-7" novalidate @submit.prevent="submit">
        <div class="grid gap-6 md:grid-cols-2">
            <label class="block text-sm font-semibold text-[#264440]">
                Client name
                <input
                    v-model="form.client_name"
                    type="text"
                    maxlength="255"
                    class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                    :aria-invalid="Boolean(errorFor('client_name'))"
                    aria-describedby="client-name-error"
                />
                <span
                    v-if="errorFor('client_name')"
                    id="client-name-error"
                    class="mt-1.5 block text-sm text-[#a43d27]"
                    >{{ errorFor('client_name') }}</span
                >
            </label>
            <label class="block text-sm font-semibold text-[#264440]">
                Project name
                <input
                    v-model="form.project_name"
                    type="text"
                    maxlength="255"
                    class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                    :aria-invalid="Boolean(errorFor('project_name'))"
                    aria-describedby="project-name-error"
                />
                <span
                    v-if="errorFor('project_name')"
                    id="project-name-error"
                    class="mt-1.5 block text-sm text-[#a43d27]"
                    >{{ errorFor('project_name') }}</span
                >
            </label>
        </div>

        <label class="block text-sm font-semibold text-[#264440]">
            Description
            <textarea
                v-model="form.description"
                rows="5"
                class="mt-2 block w-full resize-y border border-[#b9c6b8] bg-white px-3 py-2.5 leading-6 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
            />
        </label>

        <div class="grid gap-6 md:grid-cols-2">
            <label class="block text-sm font-semibold text-[#264440]">
                Status
                <select
                    v-model="form.status"
                    class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                >
                    <option
                        v-for="status in statuses"
                        :key="status"
                        :value="status"
                    >
                        {{ status }}
                    </option>
                </select>
            </label>
            <label class="block text-sm font-semibold text-[#264440]">
                Priority
                <select
                    v-model="form.priority"
                    class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                >
                    <option
                        v-for="priority in priorities"
                        :key="priority"
                        :value="priority"
                    >
                        {{ priority }}
                    </option>
                </select>
            </label>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <label class="block text-sm font-semibold text-[#264440]"
                >Start date<input
                    v-model="form.start_date"
                    type="date"
                    class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
            /></label>
            <label class="block text-sm font-semibold text-[#264440]"
                >Due date<input
                    v-model="form.due_date"
                    type="date"
                    class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                    :aria-invalid="Boolean(errorFor('due_date'))"
                    aria-describedby="due-date-error"
                /><span
                    v-if="errorFor('due_date')"
                    id="due-date-error"
                    class="mt-1.5 block text-sm text-[#a43d27]"
                    >{{ errorFor('due_date') }}</span
                ></label
            >
        </div>

        <div
            class="flex flex-col-reverse gap-3 border-t border-[#d9ddd4] pt-6 sm:flex-row sm:justify-end"
        >
            <a
                href="/projects"
                class="inline-flex min-h-11 items-center justify-center px-5 text-sm font-semibold text-[#48615c] hover:text-[#173f3b]"
                >Cancel</a
            >
            <button
                type="submit"
                class="inline-flex min-h-11 items-center justify-center bg-[#d95d39] px-5 text-sm font-semibold text-white hover:bg-[#bc4728] disabled:cursor-wait disabled:opacity-60"
                :disabled="isSubmitting"
            >
                {{ isSubmitting ? submittingLabel : submitLabel }}
            </button>
        </div>
    </form>
</template>
