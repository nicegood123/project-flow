<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import ProjectForm from '@/components/projects/ProjectForm.vue';
import {
    ApiError,
    getProject,
    type Project,
    type ProjectPayload,
    updateProject,
} from '@/lib/projects';

const props = defineProps<{
    projectId: number;
}>();

const project = ref<Project | null>(null);
const isLoading = ref(true);
const isSubmitting = ref(false);
const isNotFound = ref(false);
const loadError = ref<string | null>(null);
const saveError = ref<string | null>(null);
const serverErrors = ref<Record<string, string[]>>({});

async function loadProject(): Promise<void> {
    isLoading.value = true;
    loadError.value = null;
    isNotFound.value = false;

    try {
        project.value = await getProject(props.projectId);
    } catch (requestError) {
        if (requestError instanceof ApiError && requestError.status === 404) {
            isNotFound.value = true;
        } else {
            loadError.value = 'Unable to load this project. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
}

async function saveProject(payload: ProjectPayload): Promise<void> {
    isSubmitting.value = true;
    saveError.value = null;
    serverErrors.value = {};

    try {
        await updateProject(props.projectId, payload);
        router.visit('/projects');
    } catch (requestError) {
        if (requestError instanceof ApiError && requestError.status === 401) {
            router.visit('/login');
            return;
        }

        if (requestError instanceof ApiError) {
            saveError.value = requestError.message;
            serverErrors.value = requestError.errors;
        } else {
            saveError.value = 'Unable to save project. Please try again.';
        }
    } finally {
        isSubmitting.value = false;
    }
}

onMounted(loadProject);
</script>

<template>
    <Head title="Edit project" />

    <main class="min-h-screen bg-[#f4f5ef] text-[#1d2a28]">
        <section class="border-b border-[#d9ddd4] bg-[#fbfcf8]">
            <div class="mx-auto max-w-3xl px-5 py-10 sm:px-8 lg:px-10">
                <a
                    href="/projects"
                    class="text-sm font-semibold text-[#246b62] hover:text-[#173f3b]"
                    >Back to projects</a
                >
                <p
                    class="mt-7 text-sm font-semibold tracking-[0.12em] text-[#5d7c69] uppercase"
                >
                    Project flow
                </p>
                <h1 class="mt-2 text-4xl font-semibold text-[#173f3b]">
                    Edit project
                </h1>
            </div>
        </section>

        <section class="mx-auto max-w-3xl px-5 py-8 sm:px-8 lg:px-10">
            <div
                v-if="isLoading"
                class="border border-[#d9ddd4] bg-white p-8"
                role="status"
            >
                <div class="h-6 w-52 animate-pulse bg-[#e5e8df]" />
                <div class="mt-6 h-11 animate-pulse bg-[#f0f1eb]" />
                <div class="mt-5 h-11 animate-pulse bg-[#f0f1eb]" />
            </div>

            <div
                v-else-if="isNotFound"
                class="border border-[#d9ddd4] bg-white px-6 py-14 text-center"
            >
                <p class="text-lg font-semibold text-[#173f3b]">
                    Project not found
                </p>
                <p class="mt-2 text-sm text-[#61706c]">
                    It may have been deleted or the link is no longer valid.
                </p>
                <a
                    href="/projects"
                    class="mt-6 inline-flex min-h-10 items-center bg-[#173f3b] px-4 text-sm font-semibold text-white hover:bg-[#0e302d]"
                    >Return to projects</a
                >
            </div>

            <div
                v-else-if="loadError"
                class="border border-[#e5b6a5] bg-[#fff7f3] p-8"
                role="alert"
            >
                <p class="text-lg font-semibold text-[#8b321b]">
                    Project could not be loaded
                </p>
                <p class="mt-2 text-sm text-[#7a4a3e]">{{ loadError }}</p>
                <button
                    type="button"
                    class="mt-5 min-h-10 border border-[#8b321b] px-4 text-sm font-semibold text-[#8b321b] hover:bg-[#fdeae2]"
                    @click="loadProject"
                >
                    Try again
                </button>
            </div>

            <template v-else-if="project">
                <div
                    v-if="saveError"
                    class="mb-6 border border-[#e5b6a5] bg-[#fff7f3] px-5 py-4 text-sm text-[#8b321b]"
                    role="alert"
                >
                    {{ saveError }}
                </div>
                <div class="border border-[#d9ddd4] bg-white p-6 sm:p-8">
                    <ProjectForm
                        :initial-values="project"
                        :is-submitting="isSubmitting"
                        :server-errors="serverErrors"
                        submit-label="Save changes"
                        submitting-label="Saving changes"
                        @submit="saveProject"
                    />
                </div>
            </template>
        </section>
    </main>
</template>
