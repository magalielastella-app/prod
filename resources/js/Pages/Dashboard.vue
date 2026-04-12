<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: { type: Object, required: true },
    alerts: { type: Array, default: () => [] },
    pendingTasks: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Tableau de bord" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tableau de bord
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Statistiques -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">Produits</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.products }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">Stock bas</div>
                        <div class="mt-1 text-2xl font-bold text-amber-600">{{ stats.lowStock }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">Périmés / à venir</div>
                        <div class="mt-1 text-2xl font-bold text-red-600">{{ stats.expiring }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">Employés</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.employees }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">Heures semaine</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.weeklyHours }} h</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">Contrôles du jour</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.hygieneChecks }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <!-- Alertes -->
                    <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                        <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-gray-100">
                            Alertes récentes
                        </h3>
                        <ul v-if="alerts.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li v-for="(a, i) in alerts" :key="i"
                                class="flex items-center justify-between py-2 text-sm text-gray-700 dark:text-gray-200">
                                <span>{{ a.label }}</span>
                                <StatusBadge :label="a.type === 'danger' ? 'Critique' : 'Attention'" :cls="a.type" />
                            </li>
                        </ul>
                        <p v-else class="text-sm italic text-gray-500">
                            Aucune alerte — tout est en ordre ✓
                        </p>
                    </div>

                    <!-- Tâches hygiène en attente -->
                    <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                        <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-gray-100">
                            Tâches d'hygiène en attente
                        </h3>
                        <ul v-if="pendingTasks.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li v-for="t in pendingTasks" :key="t.id"
                                class="flex items-center justify-between py-2 text-sm text-gray-700 dark:text-gray-200">
                                <span>
                                    {{ t.zone }}
                                    <small class="ml-1 text-gray-500">({{ t.frequency }})</small>
                                </span>
                                <StatusBadge :label="t.status.label" :cls="t.status.cls" />
                            </li>
                        </ul>
                        <p v-else class="text-sm italic text-gray-500">
                            Aucune tâche en attente ✓
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
