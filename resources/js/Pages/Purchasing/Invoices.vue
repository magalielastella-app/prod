<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

defineProps({
    invoices: { type: Object, required: true }, // Paginator
});

function destroy(inv) {
    if (!confirm('Supprimer cette facture ? Les entrées de stock associées seront annulées.')) return;
    router.delete(route('invoices.destroy', inv.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Factures fournisseurs" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Factures fournisseurs</h2>
                <Link :href="route('invoices.create')">
                    <PrimaryButton>+ Nouvelle facture</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <div class="overflow-x-auto rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Fournisseur</th>
                                <th class="px-4 py-3">N° facture</th>
                                <th class="px-4 py-3">Lignes</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="!invoices.data.length">
                                <td colspan="6" class="px-4 py-8 text-center italic text-gray-500">Aucune facture.</td>
                            </tr>
                            <tr v-for="inv in invoices.data" :key="inv.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                <td class="px-4 py-3">{{ new Date(inv.date).toLocaleDateString('fr-FR') }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ inv.supplier?.name }}</td>
                                <td class="px-4 py-3">{{ inv.invoice_number || '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ inv.items?.length || 0 }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ Number(inv.total).toFixed(2) }} €</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        <Link :href="route('invoices.show', inv.id)"
                                            class="rounded border border-gray-300 px-2 py-1 text-xs">Voir</Link>
                                        <DangerButton @click="destroy(inv)" class="!px-2 !py-1 text-xs">Suppr</DangerButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
