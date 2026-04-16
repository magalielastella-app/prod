<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    templates: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Trames d'entretien" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Trames d'entretien
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
                <p class="text-sm text-gray-600">
                    Chaque trame contient les sections et questions qui apparaissent dans le formulaire d'entretien.
                    Vous pouvez modifier les libellés des questions en cliquant sur « Modifier ».
                </p>

                <div class="overflow-hidden rounded-xl bg-white shadow-soft dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-4 py-3">Trame</th>
                                <th class="px-4 py-3">Clé</th>
                                <th class="px-4 py-3 text-center">Sections</th>
                                <th class="px-4 py-3 text-center">Questions</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="t in templates" :key="t.key" class="text-sm text-gray-700 dark:text-gray-200">
                                <td class="px-4 py-3 font-medium">{{ t.label }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ t.key }}</td>
                                <td class="px-4 py-3 text-center">{{ t.section_count }}</td>
                                <td class="px-4 py-3 text-center">{{ t.field_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    <Link :href="route('templates.edit', t.key)"
                                        class="text-brand-primary hover:underline">
                                        Modifier
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
