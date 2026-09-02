<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import ProjectPriorityIndicator from '@/components/projects/ProjectPriorityIndicator.vue';
import ProjectStatusIndicator from '@/components/projects/ProjectStatusIndicator.vue';
import {
    ApiError,
    deleteProject,
    getProjects,
    type Project,
} from '@/lib/projects';

const projects = ref<Project[]>([]);
const isLoading = ref(true);
const loadError = ref<string | null>(null);
const deleteError = ref<string | null>(null);
const successMessage = ref<string | null>(null);
const deletingId = ref<number | null>(null);
const search = ref('');
const status = ref<Project['status'] | ''>('');
const priority = ref<Project['priority'] | ''>('');
const sort = ref<
    | 'project_name'
    | 'client_name'
    | 'start_date'
    | 'due_date'
    | 'priority'
    | 'status'
    | ''
>('');
const direction = ref<'asc' | 'desc'>('asc');
let searchTimeout: number | undefined;

const projectCount = computed(() =>
    projects.value.length === 1
        ? '1 project'
        : `${projects.value.length} projects`,
);

function formatDate(date: string | null): string {
    if (!date) {
        return 'Not scheduled';
    }

    return new Intl.DateTimeFormat('en-US', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(`${date}T00:00:00`));
}

async function loadProjects(): Promise<void> {
    isLoading.value = true;
    loadError.value = null;

    try {
        projects.value = await getProjects({
            search: search.value,
            status: status.value,
            priority: priority.value,
            sort: sort.value,
            direction: direction.value,
        });
    } catch (requestError) {
        if (requestError instanceof ApiError && requestError.status === 401) {
            router.visit('/login');
            return;
        }

        loadError.value =
            requestError instanceof Error
                ? requestError.message
                : 'Unable to load projects.';
    } finally {
        isLoading.value = false;
    }
}

async function removeProject(project: Project): Promise<void> {
    if (
        !window.confirm(
            `Delete ${project.project_name}? This cannot be undone.`,
        )
    ) {
        return;
    }

    deletingId.value = project.id;
    deleteError.value = null;
    successMessage.value = null;

    try {
        await deleteProject(project.id);
        projects.value = projects.value.filter(({ id }) => id !== project.id);
        successMessage.value = `${project.project_name} was deleted.`;
    } catch (requestError) {
        if (requestError instanceof ApiError && requestError.status === 401) {
            router.visit('/login');
            return;
        }

        deleteError.value =
            requestError instanceof Error
                ? requestError.message
                : 'Unable to delete project.';
    } finally {
        deletingId.value = null;
    }
}

function logout(): void {
    router.post('/logout');
}

function clearFilters(): void {
    search.value = '';
    status.value = '';
    priority.value = '';
    sort.value = '';
    direction.value = 'asc';
}

watch([search, status, priority, sort, direction], () => {
    window.clearTimeout(searchTimeout);
    searchTimeout = window.setTimeout(loadProjects, 250);
});

onMounted(loadProjects);
onBeforeUnmount(() => window.clearTimeout(searchTimeout));
</script>

<template>
    <Head title="Projects" />

    <main class="min-h-screen bg-[#f4f5ef] text-[#1d2a28]">
        <section class="border-b border-[#d9ddd4] bg-[#fbfcf8]">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-7 px-5 py-10 sm:px-8 lg:px-10"
            >
                <div
                    class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end"
                >
                    <div>
                        <p
                            class="text-sm font-semibold tracking-[0.12em] text-[#5d7c69] uppercase"
                        >
                            Project flow
                        </p>
                        <h1
                            class="mt-2 text-4xl font-semibold tracking-normal text-[#173f3b]"
                        >
                            Projects
                        </h1>
                        <p class="mt-2 text-sm text-[#61706c]">
                            {{ isLoading ? 'Loading workspace' : projectCount }}
                        </p>
                    </div>
                    <a
                        href="/projects/create"
                        class="inline-flex min-h-11 items-center justify-center bg-[#d95d39] px-5 text-sm font-semibold text-white transition hover:bg-[#bc4728] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#173f3b]"
                    >
                        Create project
                    </a>
                    <button
                        type="button"
                        class="text-sm font-semibold text-[#246b62] hover:text-[#173f3b]"
                        @click="logout"
                    >
                        Sign out
                    </button>
                </div>
                <div class="grid max-w-5xl gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <label class="text-sm font-semibold text-[#264440]">
                        Search projects
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Client or project name"
                            class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none placeholder:text-[#71817b] focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                        />
                    </label>
                    <label class="text-sm font-semibold text-[#264440]">
                        Status
                        <select
                            v-model="status"
                            class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                        >
                            <option value="">All statuses</option>
                            <option value="Planning">Planning</option>
                            <option value="In Progress">In Progress</option>
                            <option value="On Hold">On Hold</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </label>
                    <label class="text-sm font-semibold text-[#264440]">
                        Priority
                        <select
                            v-model="priority"
                            class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                        >
                            <option value="">All priorities</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </label>
                    <label class="text-sm font-semibold text-[#264440]">
                        Sort by
                        <select
                            v-model="sort"
                            class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                        >
                            <option value="">Default order</option>
                            <option value="project_name">Project name</option>
                            <option value="client_name">Client name</option>
                            <option value="start_date">Start date</option>
                            <option value="due_date">Due date</option>
                            <option value="priority">Priority</option>
                            <option value="status">Status</option>
                        </select>
                    </label>
                    <label class="text-sm font-semibold text-[#264440]">
                        Direction
                        <select
                            v-model="direction"
                            :disabled="!sort"
                            class="mt-2 block min-h-11 w-full border border-[#b9c6b8] bg-white px-3 text-[#173f3b] outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce] disabled:cursor-not-allowed disabled:bg-[#edf0e8]"
                        >
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </label>
                </div>
                <button
                    v-if="search || status || priority || sort"
                    type="button"
                    class="w-fit text-sm font-semibold text-[#246b62] hover:text-[#173f3b]"
                    @click="clearFilters"
                >
                    Clear filters
                </button>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-10">
            <div
                v-if="isLoading"
                class="border border-[#d9ddd4] bg-white p-8"
                role="status"
            >
                <div class="h-5 w-40 animate-pulse bg-[#e5e8df]" />
                <div class="mt-5 h-16 animate-pulse bg-[#f0f1eb]" />
                <div class="mt-3 h-16 animate-pulse bg-[#f0f1eb]" />
            </div>

            <div
                v-else-if="loadError"
                class="border border-[#e5b6a5] bg-[#fff7f3] p-8"
                role="alert"
            >
                <p class="text-lg font-semibold text-[#8b321b]">
                    Projects could not be loaded
                </p>
                <p class="mt-2 text-sm text-[#7a4a3e]">{{ loadError }}</p>
                <button
                    type="button"
                    class="mt-5 min-h-10 border border-[#8b321b] px-4 text-sm font-semibold text-[#8b321b] hover:bg-[#fdeae2]"
                    @click="loadProjects"
                >
                    Try again
                </button>
            </div>

            <div
                v-else-if="projects.length === 0"
                class="border border-dashed border-[#b9c6b8] bg-[#fbfcf8] px-6 py-16 text-center"
            >
                <p class="text-lg font-semibold text-[#173f3b]">
                    No projects yet
                </p>
                <p class="mt-2 text-sm text-[#61706c]">
                    {{
                        search || status || priority || sort
                            ? 'Try a different search term.'
                            : 'Create a project to begin tracking work and deadlines.'
                    }}
                </p>
                <a
                    href="/projects/create"
                    class="mt-6 inline-flex min-h-10 items-center bg-[#173f3b] px-4 text-sm font-semibold text-white hover:bg-[#0e302d]"
                    >Create project</a
                >
            </div>

            <div
                v-else
                class="overflow-x-auto border border-[#d9ddd4] bg-white"
            >
                <div
                    v-if="deleteError"
                    class="border-b border-[#e5b6a5] bg-[#fff7f3] px-5 py-3 text-sm text-[#8b321b]"
                    role="alert"
                >
                    {{ deleteError }}
                </div>
                <div
                    v-if="successMessage"
                    class="border-b border-[#b8d7ce] bg-[#eff9f5] px-5 py-3 text-sm text-[#28563a]"
                    role="status"
                >
                    {{ successMessage }}
                </div>
                <table
                    class="w-full min-w-[980px] border-collapse text-left text-sm"
                >
                    <thead
                        class="border-b border-[#d9ddd4] bg-[#edf0e8] text-xs font-semibold tracking-[0.08em] text-[#53625b] uppercase"
                    >
                        <tr>
                            <th class="px-5 py-4">Project</th>
                            <th class="px-5 py-4">Description</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Priority</th>
                            <th class="px-5 py-4">Start date</th>
                            <th class="px-5 py-4">Due date</th>
                            <th class="px-5 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e8df]">
                        <tr
                            v-for="project in projects"
                            :key="project.id"
                            class="transition hover:bg-[#fbfcf8]"
                        >
                            <td class="px-5 py-5">
                                <p class="font-semibold text-[#173f3b]">
                                    {{ project.project_name }}
                                </p>
                                <p class="mt-1 text-[#65746e]">
                                    {{ project.client_name }}
                                </p>
                            </td>
                            <td
                                class="max-w-xs px-5 py-5 leading-6 text-[#53625b]"
                            >
                                {{ project.description || 'No description' }}
                            </td>
                            <td class="px-5 py-5">
                                <ProjectStatusIndicator
                                    :status="project.status"
                                />
                            </td>
                            <td class="px-5 py-5">
                                <ProjectPriorityIndicator
                                    :priority="project.priority"
                                />
                            </td>
                            <td class="px-5 py-5 text-[#53625b]">
                                {{ formatDate(project.start_date) }}
                            </td>
                            <td class="px-5 py-5 text-[#53625b]">
                                {{ formatDate(project.due_date) }}
                            </td>
                            <td class="px-5 py-5">
                                <div class="flex justify-end gap-3">
                                    <a
                                        :href="`/projects/${project.id}/edit`"
                                        class="font-semibold text-[#246b62] hover:text-[#173f3b]"
                                        >Edit</a
                                    >
                                    <button
                                        type="button"
                                        class="font-semibold text-[#b63c25] hover:text-[#8b321b] disabled:cursor-wait disabled:opacity-60"
                                        :disabled="deletingId === project.id"
                                        @click="removeProject(project)"
                                    >
                                        {{
                                            deletingId === project.id
                                                ? 'Deleting'
                                                : 'Delete'
                                        }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</template>
