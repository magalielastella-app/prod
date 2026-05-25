<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: { type: Object, required: true },
    recentCandidates: { type: Array, default: () => [] },
    upcomingInterviews: { type: Array, default: () => [] },
});

const page = usePage();
const userName = computed(() => page.props.auth.user?.name || '');

const statusColor = (status) => {
    const map = {
        new: 'info',
        screening: 'warn',
        interview: 'warn',
        offer: 'ok',
        hired: 'ok',
        rejected: 'danger',
    };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const map = {
        new: 'Nouveau',
        screening: 'Tri',
        interview: 'Entretien',
        offer: 'Offre',
        hired: 'Embauché',
        rejected: 'Refusé',
    };
    return map[status] || status;
};

const statCards = computed(() => [
    {
        label: 'Candidats',
        value: props.stats.total_candidates,
        bgCard: 'bg-brand-tertiary',
        bgIcon: 'bg-white/70',
        iconColor: 'text-teal-700',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    },
    {
        label: 'Offres actives',
        value: props.stats.active_offers,
        bgCard: 'bg-brand-lavender',
        bgIcon: 'bg-white/70',
        iconColor: 'text-violet-700',
        icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    },
    {
        label: 'Entretiens cette semaine',
        value: props.stats.interviews_this_week,
        bgCard: 'bg-brand-peach',
        bgIcon: 'bg-white/70',
        iconColor: 'text-orange-700',
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    },
    {
        label: 'Analyses IA',
        value: props.stats.analyses_saved,
        bgCard: 'bg-brand-sky',
        bgIcon: 'bg-white/70',
        iconColor: 'text-sky-700',
        icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    },
]);
</script>

<template>
    <Head title="Tableau de bord" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-brand-primary">
                    OSCD Recrutement
                </span>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">
                    Bonjour {{ userName }}
                </h2>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Tableau de bord du recrutement
                </span>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

                <!-- Statistiques -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div v-for="(c, i) in statCards" :key="i"
                        :class="['group relative overflow-hidden rounded-xl p-4 shadow-soft transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md', c.bgCard]">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wider text-slate-700/80">{{ c.label }}</div>
                                <div class="mt-1 text-3xl font-bold text-slate-900">{{ c.value }}</div>
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

                <div class="grid gap-6 lg:grid-cols-2">
                    <!-- Derniers candidats -->
                    <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-gray-100">
                                <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-tertiary text-brand-primary">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </span>
                                Derniers candidats
                            </h3>
                            <Link :href="route('candidates.index')"
                                class="text-sm font-medium text-brand-primary transition hover:text-brand-primary-dark hover:underline">
                                Voir tout
                            </Link>
                        </div>
                        <ul v-if="recentCandidates.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li v-for="c in recentCandidates" :key="c.id"
                                class="group flex items-center justify-between py-3 text-sm transition hover:bg-brand-tertiary/40 rounded -mx-2 px-2">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-tertiary text-xs font-bold uppercase text-brand-primary">
                                        {{ c.full_name.split(' ').map(w => w[0]).slice(0, 2).join('') }}
                                    </span>
                                    <div>
                                        <Link :href="route('candidates.show', c.id)"
                                            class="font-medium text-gray-900 group-hover:text-brand-primary dark:text-gray-100">
                                            {{ c.full_name }}
                                        </Link>
                                        <div class="text-xs text-gray-500">{{ c.email || 'Pas d\'email' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-500">{{ c.created_at }}</span>
                                    <StatusBadge :label="statusLabel(c.status)" :cls="statusColor(c.status)" />
                                </div>
                            </li>
                        </ul>
                        <div v-else class="py-8 text-center">
                            <p class="text-sm italic text-gray-500">Aucun candidat pour le moment</p>
                        </div>
                    </div>

                    <!-- Prochains entretiens -->
                    <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-gray-100">
                                <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-peach text-orange-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                Prochains entretiens
                            </h3>
                            <Link :href="route('agenda.index')"
                                class="text-sm font-medium text-brand-primary transition hover:text-brand-primary-dark hover:underline">
                                Voir l'agenda
                            </Link>
                        </div>
                        <ul v-if="upcomingInterviews.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li v-for="e in upcomingInterviews" :key="e.id"
                                class="flex items-center justify-between py-3 text-sm rounded -mx-2 px-2 hover:bg-brand-peach/30 transition">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ e.title }}</div>
                                    <div class="text-xs text-gray-500">{{ e.candidate_name }} <span v-if="e.job_offer_title">- {{ e.job_offer_title }}</span></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-medium text-gray-700">{{ e.scheduled_at }}</div>
                                    <div v-if="e.location" class="text-xs text-gray-500">{{ e.location }}</div>
                                </div>
                            </li>
                        </ul>
                        <div v-else class="py-8 text-center">
                            <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-emerald-100 text-emerald-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="mt-3 text-sm italic text-gray-500">Aucun entretien planifié</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
