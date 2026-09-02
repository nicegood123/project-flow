<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ProjectForm from '@/components/projects/ProjectForm.vue';
import { ApiError, createProject, type ProjectPayload } from '@/lib/projects';

const isSubmitting = ref(false);
const error = ref<string | null>(null);
const serverErrors = ref<Record<string, string[]>>({});

async function submitProject(project: ProjectPayload): Promise<void> {
    isSubmitting.value = true;
    error.value = null;
    serverErrors.value = {};

    try {
        await createProject(project);
        router.visit('/projects');
    } catch (submissionError) {
        if (
            submissionError instanceof ApiError &&
            submissionError.status === 401
        ) {
            router.visit('/login');
            return;
        }

        if (submissionError instanceof ApiError) {
            error.value = submissionError.message;
            serverErrors.value = submissionError.errors;
        } else {
            error.value = 'Unable to create project. Please try again.';
        }
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <Head title="Create project" />

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
                    Create project
                </h1>
                <p class="mt-2 text-sm text-[#61706c]">
                    Capture the work, its owner, and the dates that matter.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-3xl px-5 py-8 sm:px-8 lg:px-10">
            <div
                v-if="error"
                class="mb-6 border border-[#e5b6a5] bg-[#fff7f3] px-5 py-4 text-sm text-[#8b321b]"
                role="alert"
            >
                {{ error }}
            </div>
            <div class="border border-[#d9ddd4] bg-white p-6 sm:p-8">
                <ProjectForm
                    :is-submitting="isSubmitting"
                    :server-errors="serverErrors"
                    submitting-label="Creating project"
                    @submit="submitProject"
                />
            </div>
        </section>
    </main>
</template>
