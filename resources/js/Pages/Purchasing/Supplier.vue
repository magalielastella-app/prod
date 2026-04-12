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

const props = defineProps({
    supplier: { type: Object, required: true },
    products: { type: Array, required: true },
});

const showModal = ref(false);
const editingId = ref(null);
const form = useForm({
    product_id: '', reference: '', name: '', unit: '',
    pack_size: null, price: null, usual_quantity: null,
});

function openCreate() {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}
function openEdit(line) {
    editingId.value = line.id;
    form.clearErrors();
    Object.assign(form, {
        product_id: line.product_id || '',
        reference: line.reference || '',
        name: line.name,
        unit: line.unit || '',
        pack_size: line.pack_size,
        price: line.price,
        usual_quantity: line.usual_quantity,
    });
    showModal.value = true;
}
function submit() {
    const opts = { preserveScroll: true, onSuccess: () => (showModal.value = false) };
    if (editingId.value) {
        form.put(route('cadencier.update', editingId.value), opts);
    } else {
        form.post(route('cadencier.store', props.supplier.id), opts);
    }
}
function destroy(line) {
    if (!confirm(`Retirer "${line.name}" du cadencier ?`)) return;
    router.delete(route('cadencier.destroy', line.id), { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Cadencier — ${supplier.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Cadencier — {{ supplier.name }}
                </h2>
                <Link :href="route('suppliers.index')" class="text-sm text-gray-500 underline">← Retour aux fournisseurs</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <!-- Infos fournisseur -->
                <div class="grid grid-cols-1 gap-4 rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800 md:grid-cols-3">
                    <div>
                        <div class="text-xs uppercase text-gray-500">Contact</div>
                        <div>{{ supplier.contact || '—' }}</div>
                        <div class="text-sm text-gray-500">{{ supplier.email || supplier.phone }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase text-gray-500">Jours de commande</div>
                        <div>{{ supplier.order_day || '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase text-gray-500">Notes</div>
                        <div class="whitespace-pre-line text-sm text-gray-600 dark:text-gray-300">{{ supplier.notes || '—' }}</div>
                    </div>
                </div>

                <!-- Cadencier -->
                <div class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Cadencier</h3>
                        <PrimaryButton @click="openCreate">+ Ajouter un produit au cadencier</PrimaryButton>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <th class="px-4 py-3">Référence</th>
                                    <th class="px-4 py-3">Produit</th>
                                    <th class="px-4 py-3">Lié à l'inventaire</th>
                                    <th class="px-4 py-3">Unité</th>
                                    <th class="px-4 py-3">Colisage</th>
                                    <th class="px-4 py-3">Prix unitaire</th>
                                    <th class="px-4 py-3">Qté habituelle</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-if="!supplier.cadencier?.length">
                                    <td colspan="8" class="px-4 py-8 text-center italic text-gray-500">
                                        Cadencier vide. Ajoutez les produits récurrents de ce fournisseur.
                                    </td>
                                </tr>
                                <tr v-for="line in supplier.cadencier" :key="line.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ line.reference || '—' }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ line.name }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                        {{ line.product?.name || '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ line.unit || '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ line.pack_size ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                        {{ line.price != null ? Number(line.price).toFixed(2) + ' €' : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ line.usual_quantity ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-1">
                                            <SecondaryButton @click="openEdit(line)" class="!px-2 !py-1 text-xs">Modifier</SecondaryButton>
                                            <DangerButton @click="destroy(line)" class="!px-2 !py-1 text-xs">Suppr</DangerButton>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Factures -->
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-gray-100">Factures de ce fournisseur</h3>
                    <ul v-if="supplier.invoices?.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li v-for="inv in supplier.invoices" :key="inv.id" class="flex items-center justify-between py-2 text-sm">
                            <Link :href="route('invoices.show', inv.id)" class="font-medium underline">
                                {{ inv.invoice_number || 'Facture #' + inv.id }}
                            </Link>
                            <div class="flex items-center gap-4">
                                <span class="text-gray-500">{{ new Date(inv.date).toLocaleDateString('fr-FR') }}</span>
                                <span class="font-semibold">{{ Number(inv.total).toFixed(2) }} €</span>
                            </div>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucune facture pour ce fournisseur.</p>
                </div>
            </div>
        </div>

        <DataModal :show="showModal" :title="editingId ? 'Modifier la ligne' : 'Nouvelle ligne de cadencier'" @close="showModal = false">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel value="Nom (dénomination cadencier) *" />
                    <TextInput v-model="form.name" required class="mt-1 block w-full" />
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Référence fournisseur" />
                        <TextInput v-model="form.reference" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Produit de l'inventaire (optionnel)" />
                        <select v-model="form.product_id"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                            <option value="">— aucun —</option>
                            <option v-for="p in products" :key="p.id" :value="p.id">
                                {{ p.name }} ({{ p.unit || '—' }})
                            </option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <InputLabel value="Unité" />
                        <TextInput v-model="form.unit" placeholder="kg, L, pcs" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Colisage" />
                        <TextInput v-model="form.pack_size" type="number" step="0.01" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Qté habituelle" />
                        <TextInput v-model="form.usual_quantity" type="number" step="0.01" class="mt-1 block w-full" />
                    </div>
                </div>
                <div>
                    <InputLabel value="Prix unitaire (€)" />
                    <TextInput v-model="form.price" type="number" step="0.01" class="mt-1 block w-full" />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                </div>
            </form>
        </DataModal>
    </AuthenticatedLayout>
</template>
