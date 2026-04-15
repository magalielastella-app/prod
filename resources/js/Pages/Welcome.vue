<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean, default: true },
    canRegister: { type: Boolean, default: true },
});
</script>

<template>
    <Head title="Cabinet Dentaire de l'Obiou — Entretiens annuels" />
    <div class="relative min-h-screen overflow-hidden bg-brand-cream">
        <!-- Taches colorées d'arrière-plan -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-20 -left-20 h-96 w-96 rounded-full bg-brand-primary-light/30 blur-3xl" />
            <div class="absolute top-1/3 -right-20 h-96 w-96 rounded-full bg-brand-coral/20 blur-3xl" />
            <div class="absolute bottom-0 left-1/3 h-96 w-96 rounded-full bg-brand-sky/25 blur-3xl" />
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-5xl flex-col items-center justify-center px-6 py-16">
            <BrandLogo variant="full" size="xl" class="animate-fade-in shadow-xl" />

            <h1 class="mt-8 text-center text-4xl font-bold tracking-tight text-brand-dark sm:text-5xl">
                Entretiens
                <span class="bg-gradient-to-r from-brand-primary to-brand-sky bg-clip-text text-transparent">
                    annuels
                </span>
            </h1>
            <p class="mt-4 max-w-xl text-center text-lg text-slate-600">
                Plateforme interne pour planifier, conduire et signer les entretiens annuels
                de toute l'équipe.
            </p>

            <div class="mt-10 flex flex-wrap justify-center gap-3">
                <Link v-if="canLogin" :href="route('login')"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                    Se connecter
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </Link>
                <Link v-if="canRegister" :href="route('register')"
                    class="inline-flex items-center gap-2 rounded-xl border-2 border-brand-primary bg-white px-6 py-3 text-sm font-semibold text-brand-primary transition hover:-translate-y-0.5 hover:bg-brand-tertiary hover:shadow-md">
                    Créer un compte
                </Link>
            </div>

            <!-- 3 mini-cartes de présentation -->
            <div class="mt-16 grid w-full max-w-4xl grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-white p-5 shadow-soft ring-1 ring-brand-beige/60 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-brand-tertiary text-brand-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-gray-900">Planifier</h3>
                    <p class="mt-1 text-xs text-gray-600">
                        Organisez les entretiens de toute l'équipe en quelques clics.
                    </p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-soft ring-1 ring-brand-beige/60 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-violet-100 text-brand-violet">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-gray-900">Conduire</h3>
                    <p class="mt-1 text-xs text-gray-600">
                        Chaque salarié prépare, le manager complète, la synthèse se fait à l'écran.
                    </p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-soft ring-1 ring-brand-beige/60 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-emerald-100 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-gray-900">Signer</h3>
                    <p class="mt-1 text-xs text-gray-600">
                        Signature électronique par le salarié et le manager, archivage sécurisé.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
