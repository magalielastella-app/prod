<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean, default: true },
    canRegister: { type: Boolean, default: true },
});
</script>

<template>
    <Head title="Cabinet Dentaire — Entretiens annuels" />
    <div class="min-h-screen bg-brand-cream text-brand-dark">
        <div class="mx-auto flex min-h-screen max-w-5xl flex-col items-center justify-center px-6 py-16">
            <div class="grid h-16 w-16 place-items-center rounded-full bg-brand-primary text-xl font-black text-white shadow-lg">
                CD
            </div>
            <h1 class="mt-6 text-4xl font-bold tracking-tight">Entretiens annuels</h1>
            <p class="mt-2 text-lg text-slate-600">
                Plateforme interne du cabinet dentaire pour planifier, conduire et signer les entretiens
                annuels.
            </p>

            <div class="mt-10 flex gap-3">
                <Link v-if="canLogin" :href="route('login')"
                    class="rounded-md bg-brand-primary px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-brand-primary-dark">
                    Se connecter
                </Link>
                <Link v-if="canRegister" :href="route('register')"
                    class="rounded-md border border-brand-primary px-5 py-2.5 text-sm font-semibold text-brand-primary hover:bg-brand-tertiary">
                    Créer un compte
                </Link>
            </div>
        </div>
    </div>
</template>
