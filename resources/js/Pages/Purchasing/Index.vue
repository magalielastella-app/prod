<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataModal from '@/Components/DataModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    suppliers: { type: Array, required: true },
    recentInvoices: { type: Array, default: () => [] },
});

const showModal = ref(false);
const editingId = ref(null);
const form = useForm({ name: '', contact: '', email: '', phone: '', order_day: '', notes: '' });

function openCreate() {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}
function openEdit(s) {
    editingId.value = s.id;
    form.clearErrors();
    Object.assign(form, {
        name: s.name, contact: s.contact || '', email: s.email || '',
        phone: s.phone || '', order_day: s.order_day || '', notes: s.notes || '',
    });
    showModal.value = true;
}
function submit() {
    const opts = { preserveScroll: true, onSuccess: () => (showModal.value = false) };
    if (editingId.value) {
        form.put(route('suppliers.update', editingId.value), opts);
    } else {
        form.post(route('suppliers.store'), opts);
    }
}
function destroy(s) {
    if (!confirm(`Supprimer le fournisseur "${s.name}" ?`)) return;
    router.delete(route('suppliers.destroy', s.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Achat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Achat — Fournisseurs & Cadenciers
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                    <PrimaryButton @click="openCreate">+ Nouveau fournisseur</PrimaryButton>
                    <Link :href="route('invoices.create')"
                        class="rounded-md border border-transparent bg-brand-accent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-brand-accent/90">
                        + Nouvelle facture
                    </Link>
                    <Link :href="route('invoices.index')" class="ml-auto text-sm text-gray-600 underline dark:text-gray-300">
                        Voir toutes les factures
                    </Link>
                </div>

                <!-- Fournisseurs -->
                <div class="overflow-x-auto rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-3">Fournisseur</th>
                                <th class="px-4 py-3">Contact</th>
                                <th class="px-4 py-3">Jours de commande</th>
                                <th class="px-4 py-3">Cadencier</th>
                                <th class="px-4 py-3">Factures</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="!suppliers.length">
                                <td colspan="6" class="px-4 py-8 text-center italic text-gray-500">Aucun fournisseur.</td>
                            </tr>
                            <tr v-for="s in suppliers" :key="s.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                    <Link :href="route('suppliers.show', s.id)" class="underline">{{ s.name }}</Link>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    <div>{{ s.contact || '—' }}</div>
                                    <div class="text-xs text-gray-400">{{ s.email || s.phone }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ s.order_day || '—' }}</td>
                                <td class="px-4 py-3">{{ s.cadencier_count }} produits</td>
                                <td class="px-4 py-3">{{ s.invoices_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        <SecondaryButton @click="openEdit(s)" class="!px-2 !py-1 text-xs">Modifier</SecondaryButton>
                                        <DangerButton @click="destroy(s)" class="!px-2 !py-1 text-xs">Suppr</DangerButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Dernières factures -->
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-gray-100">Dernières factures réceptionnées</h3>
                    <ul v-if="recentInvoices.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li v-for="inv in recentInvoices" :key="inv.id"
                            class="flex items-center justify-between py-2 text-sm">
                            <div>
                                <Link :href="route('invoices.show', inv.id)" class="font-medium text-gray-900 underline dark:text-gray-100">
                                    {{ inv.invoice_number || 'Facture #' + inv.id }}
                                </Link>
                                <span class="ml-2 text-gray-500">{{ inv.supplier?.name }}</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-gray-500">{{ new Date(inv.date).toLocaleDateString('fr-FR') }}</span>
                                <span class="font-semibold">{{ Number(inv.total).toFixed(2) }} €</span>
                            </div>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucune facture enregistrée.</p>
                </div>
            </div>
        </div>

        <DataModal :show="showModal" :title="editingId ? 'Modifier le fournisseur' : 'Nouveau fournisseur'" @close="showModal = false">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel value="Nom *" />
                    <TextInput v-model="form.name" required class="mt-1 block w-full" />
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Contact" />
                        <TextInput v-model="form.contact" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Téléphone" />
                        <TextInput v-model="form.phone" class="mt-1 block w-full" />
                    </div>
                </div>
                <div>
                    <InputLabel value="Email" />
                    <TextInput v-model="form.email" type="email" class="mt-1 block w-full" />
                </div>
                <div>
                    <InputLabel value="Jours de commande" />
                    <TextInput v-model="form.order_day" placeholder="Ex : Lundi, Jeudi" class="mt-1 block w-full" />
                </div>
                <div>
                    <InputLabel value="Notes" />
                    <textarea v-model="form.notes" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                </div>
            </form>
        </DataModal>
    </AuthenticatedLayout>
</template>
