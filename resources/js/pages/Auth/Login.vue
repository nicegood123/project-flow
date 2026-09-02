<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const form = reactive({
    email: '',
    password: '',
    remember: false,
});
const errors = ref<Record<string, string>>({});
const isSubmitting = ref(false);

function login(): void {
    isSubmitting.value = true;
    errors.value = {};

    router.post('/login', form, {
        onError: (formErrors) => {
            errors.value = Object.fromEntries(
                Object.entries(formErrors).map(([field, message]) => [
                    field,
                    String(message),
                ]),
            );
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
}
</script>

<template>
    <Head title="Sign in" />

    <main
        class="grid min-h-screen place-items-center bg-[#f4f5ef] px-5 py-10 text-[#1d2a28]"
    >
        <form
            class="w-full max-w-md border border-[#d9ddd4] bg-white p-7 sm:p-9"
            @submit.prevent="login"
        >
            <p
                class="text-sm font-semibold tracking-[0.12em] text-[#5d7c69] uppercase"
            >
                Project flow
            </p>
            <h1 class="mt-3 text-3xl font-semibold text-[#173f3b]">Sign in</h1>
            <p class="mt-2 text-sm text-[#61706c]">
                Use your account to manage projects.
            </p>

            <label class="mt-7 block text-sm font-semibold text-[#264440]">
                Email address
                <input
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    class="mt-2 block min-h-11 w-full border border-[#b9c6b8] px-3 outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                    :aria-invalid="Boolean(errors.email)"
                />
                <span
                    v-if="errors.email"
                    class="mt-1.5 block text-sm text-[#a43d27]"
                    >{{ errors.email }}</span
                >
            </label>
            <label class="mt-5 block text-sm font-semibold text-[#264440]">
                Password
                <input
                    v-model="form.password"
                    type="password"
                    autocomplete="current-password"
                    class="mt-2 block min-h-11 w-full border border-[#b9c6b8] px-3 outline-none focus:border-[#246b62] focus:ring-2 focus:ring-[#b8d7ce]"
                    :aria-invalid="Boolean(errors.password)"
                />
                <span
                    v-if="errors.password"
                    class="mt-1.5 block text-sm text-[#a43d27]"
                    >{{ errors.password }}</span
                >
            </label>
            <label class="mt-5 flex items-center gap-2 text-sm text-[#48615c]"
                ><input v-model="form.remember" type="checkbox" /> Remember
                me</label
            >
            <button
                type="submit"
                class="mt-7 inline-flex min-h-11 w-full items-center justify-center bg-[#d95d39] px-5 text-sm font-semibold text-white hover:bg-[#bc4728] disabled:cursor-wait disabled:opacity-60"
                :disabled="isSubmitting"
            >
                {{ isSubmitting ? 'Signing in' : 'Sign in' }}
            </button>
            <p class="mt-5 text-center text-sm text-[#61706c]">
                New to Project Flow?
                <a
                    href="/register"
                    class="font-semibold text-[#246b62] hover:text-[#173f3b]"
                    >Create an account</a
                >
            </p>
        </form>
    </main>
</template>
