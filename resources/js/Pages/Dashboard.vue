<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: { type: Object, required: true },
    upcoming: { type: Array, default: () => [] },
    myActions: { type: Array, default: () => [] },
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

// Style des cartes "Mes actions" selon le ton (urgent/primary/info)
const actionStyle = (tone) => {
    const styles = {
        urgent: {
            card: 'bg-brand-rose ring-2 ring-pink-300/70',
            icon: 'bg-white text-pink-700',
            badge: 'bg-pink-700 text-white',
            badgeLabel: 'À FAIRE MAINTENANT',
        },
        primary: {
            card: 'bg-brand-peach ring-2 ring-orange-300/70',
            icon: 'bg-white text-orange-700',
            badge: 'bg-orange-700 text-white',
            badgeLabel: 'À FAIRE',
        },
        info: {
            card: 'bg-brand-sky ring-1 ring-sky-300/60',
            icon: 'bg-white text-sky-700',
            badge: 'bg-sky-700 text-white',
            badgeLabel: 'EN COURS',
        },
    };
    return styles[tone] || styles.info;
};

// Icône SVG selon le rôle
const actionIcon = (role) => role === 'manager'
    ? 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
    : 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z';

// Chaque carte a son fond pastel et son icône pour un rendu doux et lisible.
const statCards = computed(() => [
    {
        label: `Entretiens ${props.currentYear}`,
        value: props.stats.total,
        bgCard: 'bg-brand-tertiary',
        bgIcon: 'bg-white/70',
        iconColor: 'text-teal-700',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    },
    {
        label: 'À préparer',
        value: props.stats.to_prepare,
        bgCard: 'bg-brand-peach',
        bgIcon: 'bg-white/70',
        iconColor: 'text-orange-700',
        icon: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
    },
    {
        label: 'À traiter manager',
        value: props.stats.to_review,
        bgCard: 'bg-brand-lavender',
        bgIcon: 'bg-white/70',
        iconColor: 'text-violet-700',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    },
    {
        label: 'À signer',
        value: props.stats.to_sign,
        bgCard: 'bg-brand-rose',
        bgIcon: 'bg-white/70',
        iconColor: 'text-pink-700',
        icon: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
    },
    {
        label: `Signés ${props.currentYear}`,
        value: props.stats.signed,
        bgCard: 'bg-brand-mint',
        bgIcon: 'bg-white/70',
        iconColor: 'text-emerald-700',
        icon: 'M5 13l4 4L19 7',
    },
    {
        label: 'Équipe',
        value: props.stats.team,
        bgCard: 'bg-brand-sky',
        bgIcon: 'bg-white/70',
        iconColor: 'text-sky-700',
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

                <!-- Mes actions à effectuer (très visible, en haut) -->
                <div v-if="myActions.length" class="space-y-3 animate-fade-in">
                    <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-brand-primary text-white text-sm font-bold">
                            {{ myActions.length }}
                        </span>
                        {{ myActions.length === 1 ? 'Action à effectuer' : 'Actions à effectuer' }}
                    </h3>
                    <Link v-for="a in myActions" :key="`${a.role}-${a.review_id}`"
                        :href="route('reviews.show', a.review_id)"
                        :class="['group block overflow-hidden rounded-2xl p-5 shadow-soft transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg', actionStyle(a.tone).card]">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-4">
                                <div :class="['grid h-12 w-12 shrink-0 place-items-center rounded-xl shadow-sm', actionStyle(a.tone).icon]">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="actionIcon(a.role)" />
                                    </svg>
                                </div>
                                <div>
                                    <span :class="['inline-block rounded-full px-2 py-0.5 text-[10px] font-bold tracking-wider', actionStyle(a.tone).badge]">
                                        {{ actionStyle(a.tone).badgeLabel }}
                                    </span>
                                    <h4 class="mt-1 text-lg font-bold text-slate-900">{{ a.title }}</h4>
                                    <p class="mt-1 text-sm text-slate-700">{{ a.subtitle }}</p>
                                </div>
                            </div>
                            <div class="shrink-0 self-end sm:self-center">
                                <span class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-brand-primary-dark shadow-sm transition group-hover:bg-brand-primary group-hover:text-white">
                                    {{ a.cta }}
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- État "tout est à jour" si rien à faire -->
                <div v-else class="overflow-hidden rounded-2xl bg-brand-mint p-6 shadow-soft animate-fade-in">
                    <div class="flex items-center gap-4">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-white text-emerald-700 shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-900">Tout est à jour</h4>
                            <p class="mt-1 text-sm text-slate-700">
                                Vous n'avez aucune action en attente. Bonne journée !
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bannière pastel -->
                <div class="overflow-hidden rounded-2xl bg-brand-gradient p-6 text-slate-800 shadow-soft animate-fade-in">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <h3 class="text-lg font-semibold">Campagne {{ currentYear }}</h3>
                            <p class="mt-1 text-sm text-slate-700">
                                {{ stats.total }} entretien{{ stats.total > 1 ? 's' : '' }} au total ·
                                {{ stats.signed }} déjà signé{{ stats.signed > 1 ? 's' : '' }} ·
                                {{ stats.to_sign }} prêt{{ stats.to_sign > 1 ? 's' : '' }} à signer
                            </p>
                        </div>
                        <Link :href="route('reviews.index')"
                            class="inline-flex items-center gap-2 rounded-lg bg-white/70 px-4 py-2 text-sm font-semibold text-brand-primary-dark ring-1 ring-white/60 transition hover:bg-white hover:shadow-md">
                            Aller aux entretiens
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- Statistiques (cartes pastel pleines) -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    <div v-for="(c, i) in visibleCards" :key="i"
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
