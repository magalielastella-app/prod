<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-brand-cream px-4 py-10">
        <!-- Taches colorées d'arrière-plan, très douces -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-20 -left-20 h-80 w-80 rounded-full bg-brand-primary-light/20 blur-3xl" />
            <div class="absolute bottom-0 -right-20 h-80 w-80 rounded-full bg-brand-sky/20 blur-3xl" />
        </div>

        <div class="relative">
            <Link href="/" class="inline-flex transition hover:opacity-90">
                <BrandLogo variant="full" size="lg" />
            </Link>
        </div>

        <div
            class="relative mt-6 w-full overflow-hidden rounded-xl bg-white px-6 py-6 shadow-soft ring-1 ring-brand-beige/60 sm:max-w-md sm:rounded-2xl dark:bg-gray-800"
        >
            <slot />
        </div>

        <p class="relative mt-6 text-xs text-gray-500">
            Entretiens annuels — plateforme interne
        </p>
    </div>
</template>
