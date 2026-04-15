<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: { type: Object, required: true },
    upcoming: { type: Array, default: () => [] },
    currentYear: { type: Number, required: true },
});

const page = usePage();
const userName = computed(() => page.props.auth.user?.name || '');

const statusColor = (status) => {
    if (status === 'signed') return 'ok';
    if (status === 'completed') return 'info';
    if (status === 'ready_for_manager') return 'warn';
    if (status === 'scheduled') return 'info';
    return 'warn';
};

// Chaque carte a sa couleur et son icône pour un rendu plus vivant.
const statCards = computed(() => [
    {
        label: `Entretiens ${props.currentYear}`,
        value: props.stats.total,
        color: 'from-brand-primary to-brand-primary-light',
        iconColor: 'text-brand-primary',
        bgIcon: 'bg-brand-tertiary',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    },
    {
        label: 'À préparer',
        value: props.stats.to_prepare,
        color: 'from-amber-500 to-amber-400',
        iconColor: 'text-amber-600',
        bgIcon: 'bg-amber-100',
        icon: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
    },
    {
        label: 'À traiter manager',
        value: props.stats.to_review,
        color: 'from-brand-violet to-indigo-400',
        iconColor: 'text-brand-violet',
        bgIcon: 'bg-violet-100',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    },
    {
        label: 'À signer',
        value: props.stats.to_sign,
        color: 'from-brand-coral to-brand-rose',
        iconColor: 'text-brand-rose',
        bgIcon: 'bg-rose-100',
        icon: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
    },
    {
        label: `Signés ${props.currentYear}`,
        value: props.stats.signed,
        color: 'from-emerald-500 to-brand-primary-light',
        iconColor: 'text-emerald-600',
        bgIcon: 'bg-emerald-100',
        icon: 'M5 13l4 4L19 7',
    },
    {
        label: 'Équipe',
        value: props.stats.team,
        color: 'from-brand-sky to-cyan-400',
        iconColor: 'text-brand-sky',
        bgIcon: 'bg-sky-100',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        condition: props.stats.team !== null,
    },
]);

const visibleCards = computed(() => statCards.value.filter((c) => c.condition !== false));
</script>

<template>
    <Head title="Tableau de bord" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-brand-primary">
                    Cabinet dentaire de l'Obiou
                </span>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">
                    Bonjour {{ userName }} 👋
                </h2>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Campagne des entretiens {{ currentYear }}
                </span>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

                <!-- Bannière colorée -->
                <div class="overflow-hidden rounded-2xl bg-brand-gradient p-6 text-white shadow-soft animate-fade-in">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <h3 class="text-lg font-semibold">Campagne {{ currentYear }}</h3>
                            <p class="mt-1 text-sm text-white/90">
                                {{ stats.total }} entretien{{ stats.total > 1 ? 's' : '' }} au total ·
                                {{ stats.signed }} déjà signé{{ stats.signed > 1 ? 's' : '' }} ·
                                {{ stats.to_sign }} prêt{{ stats.to_sign > 1 ? 's' : '' }} à signer
                            </p>
                        </div>
                        <Link :href="route('reviews.index')"
                            class="inline-flex items-center gap-2 rounded-lg bg-white/20 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/30 transition hover:bg-white/30 hover:shadow-lg">
                            Aller aux entretiens
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    <div v-for="(c, i) in visibleCards" :key="i"
                        class="group relative overflow-hidden rounded-xl bg-white p-4 shadow-soft transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg dark:bg-gray-800">
                        <!-- Filet coloré à gauche -->
                        <div :class="['absolute inset-y-0 left-0 w-1 bg-gradient-to-b', c.color]" />
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ c.label }}</div>
                                <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ c.value }}</div>
                            </div>
                            <div :class="['rounded-lg p-2', c.bgIcon]">
                                <svg class="h-5 w-5" :class="c.iconColor" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="c.icon" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prochains entretiens -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-gray-100">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-tertiary text-brand-primary">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                            Prochains entretiens
                        </h3>
                        <Link :href="route('reviews.index')"
                            class="text-sm font-medium text-brand-primary transition hover:text-brand-primary-dark hover:underline">
                            Voir tout →
                        </Link>
                    </div>
                    <ul v-if="upcoming.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li v-for="r in upcoming" :key="r.id"
                            class="group flex items-center justify-between py-3 text-sm transition hover:bg-brand-tertiary/40 rounded -mx-2 px-2">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-tertiary text-xs font-bold uppercase text-brand-primary">
                                    {{ (r.employee?.name || '—').split(' ').map(w => w[0]).slice(0, 2).join('') }}
                                </span>
                                <div>
                                    <Link :href="route('reviews.show', r.id)"
                                        class="font-medium text-gray-900 group-hover:text-brand-primary dark:text-gray-100">
                                        {{ r.employee?.name }}
                                    </Link>
                                    <div class="text-xs text-gray-500">{{ r.employee?.position }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">{{ r.scheduled_for || 'Non planifié' }}</span>
                                <StatusBadge :label="r.status_label" :cls="statusColor(r.status)" />
                            </div>
                        </li>
                    </ul>
                    <div v-else class="py-8 text-center">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm italic text-gray-500">
                            Aucun entretien en cours — tout est à jour
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
