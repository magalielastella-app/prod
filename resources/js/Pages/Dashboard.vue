<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
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
</script>

<template>
    <Head title="Tableau de bord" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Bonjour {{ userName }} — entretiens {{ currentYear }}
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Statistiques -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">Entretiens {{ currentYear }}</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.total }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">À préparer</div>
                        <div class="mt-1 text-2xl font-bold text-amber-600">{{ stats.to_prepare }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">À traiter manager</div>
                        <div class="mt-1 text-2xl font-bold text-indigo-600">{{ stats.to_review }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">À signer</div>
                        <div class="mt-1 text-2xl font-bold text-brand-primary">{{ stats.to_sign }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">Signés {{ currentYear }}</div>
                        <div class="mt-1 text-2xl font-bold text-emerald-600">{{ stats.signed }}</div>
                    </div>
                    <div v-if="stats.team !== null" class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">Équipe</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.team }}</div>
                    </div>
                </div>

                <!-- Prochains entretiens -->
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            Prochains entretiens
                        </h3>
                        <Link :href="route('reviews.index')" class="text-sm text-brand-primary hover:underline">
                            Voir tout →
                        </Link>
                    </div>
                    <ul v-if="upcoming.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li v-for="r in upcoming" :key="r.id" class="flex items-center justify-between py-2 text-sm">
                            <div>
                                <Link :href="route('reviews.show', r.id)"
                                    class="font-medium text-gray-900 hover:underline dark:text-gray-100">
                                    {{ r.employee?.name }}
                                </Link>
                                <span class="ml-2 text-xs text-gray-500">{{ r.employee?.position }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">{{ r.scheduled_for || 'Non planifié' }}</span>
                                <StatusBadge :label="r.status_label" :cls="statusColor(r.status)" />
                            </div>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">
                        Aucun entretien en cours — tout est à jour ✓
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
