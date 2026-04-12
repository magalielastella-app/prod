<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({ invoice: { type: Object, required: true } });
</script>

<template>
    <Head :title="`Facture ${invoice.invoice_number || invoice.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Facture {{ invoice.invoice_number || '#' + invoice.id }}
                </h2>
                <Link :href="route('invoices.index')" class="text-sm text-gray-500 underline">← Retour aux factures</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl space-y-4 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-3 gap-4 rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <div>
                        <div class="text-xs uppercase text-gray-500">Fournisseur</div>
                        <div class="font-semibold">{{ invoice.supplier?.name }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase text-gray-500">Date</div>
                        <div>{{ new Date(invoice.date).toLocaleDateString('fr-FR') }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase text-gray-500">Total</div>
                        <div class="text-xl font-bold">{{ Number(invoice.total).toFixed(2) }} €</div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-3">Libellé</th>
                                <th class="px-4 py-3">Produit lié</th>
                                <th class="px-4 py-3">Qté</th>
                                <th class="px-4 py-3">PU HT</th>
                                <th class="px-4 py-3 text-right">Total ligne</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="l in invoice.items" :key="l.id">
                                <td class="px-4 py-2 font-medium">{{ l.label }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ l.product?.name || '—' }}</td>
                                <td class="px-4 py-2">{{ l.quantity }} {{ l.unit }}</td>
                                <td class="px-4 py-2">{{ Number(l.unit_price).toFixed(2) }} €</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ Number(l.line_total).toFixed(2) }} €</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="invoice.notes" class="rounded-lg bg-white p-4 text-sm shadow-sm dark:bg-gray-800">
                    <div class="mb-1 text-xs uppercase text-gray-500">Notes</div>
                    <div class="whitespace-pre-line">{{ invoice.notes }}</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
