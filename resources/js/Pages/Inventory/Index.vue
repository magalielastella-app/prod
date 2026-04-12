<script setup>
import { ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import DataModal from '@/Components/DataModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    products: { type: Array, required: true },
    categories: { type: Array, required: true },
    filters: { type: Object, default: () => ({}) },
    recentMovements: { type: Array, default: () => [] },
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');

let searchTimer = null;
watch([search, category], () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(route('products.index'),
            { search: search.value, category: category.value },
            { preserveState: true, preserveScroll: true, replace: true }
        );
    }, 250);
});

// ---------- Formulaire produit ----------
const showModal = ref(false);
const editingId = ref(null);
const form = useForm({
    name: '', category: '', quantity: 0, unit: '',
    min_threshold: null, expiration: '', price: null, supplier: '',
});

function openCreate() {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}
function openEdit(p) {
    editingId.value = p.id;
    form.clearErrors();
    Object.assign(form, {
        name: p.name,
        category: p.category || '',
        quantity: p.quantity,
        unit: p.unit || '',
        min_threshold: p.min_threshold,
        expiration: p.expiration || '',
        price: p.price,
        supplier: p.supplier || '',
    });
    showModal.value = true;
}
function submit() {
    const opts = { preserveScroll: true, onSuccess: () => (showModal.value = false) };
    if (editingId.value) {
        form.put(route('products.update', editingId.value), opts);
    } else {
        form.post(route('products.store'), opts);
    }
}
function destroy(p) {
    if (!confirm(`Supprimer "${p.name}" ?`)) return;
    router.delete(route('products.destroy', p.id), { preserveScroll: true });
}

// ---------- Sortie de stock ----------
const showOutModal = ref(false);
const outProduct = ref(null);
const outForm = useForm({ quantity: 0, reason: 'Consommation' });
const OUT_REASONS = ['Consommation', 'Perte', 'Vol', 'Casse', 'Ajustement', 'Autre'];

function openOut(p) {
    outProduct.value = p;
    outForm.reset();
    outForm.reason = 'Consommation';
    outForm.clearErrors();
    showOutModal.value = true;
}
function submitOut() {
    outForm.post(route('stock.out', outProduct.value.id), {
        preserveScroll: true,
        onSuccess: () => (showOutModal.value = false),
    });
}
</script>

<template>
    <Head title="Inventaire" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Inventaire
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                    <TextInput v-model="search" type="search" placeholder="Rechercher..." class="w-64" />
                    <select v-model="category"
                        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        <option value="">Toutes catégories</option>
                        <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                    </select>
                    <PrimaryButton class="ml-auto" @click="openCreate">+ Nouveau produit</PrimaryButton>
                </div>

                <div class="rounded-lg bg-brand-tertiary/60 px-4 py-2 text-sm text-brand-primary">
                    ℹ L'entrée en stock est <strong>automatique</strong> à la saisie d'une facture fournisseur.
                    Utilisez le bouton « Sortie » pour une consommation ou une perte manuelle.
                </div>

                <!-- Table produits -->
                <div class="overflow-x-auto rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-3">Produit</th>
                                <th class="px-4 py-3">Catégorie</th>
                                <th class="px-4 py-3">Quantité</th>
                                <th class="px-4 py-3">Seuil</th>
                                <th class="px-4 py-3">Péremption</th>
                                <th class="px-4 py-3">Prix</th>
                                <th class="px-4 py-3">Fournisseur</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="!products.length">
                                <td colspan="9" class="px-4 py-8 text-center italic text-gray-500">Aucun produit.</td>
                            </tr>
                            <tr v-for="p in products" :key="p.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ p.name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ p.category || '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ p.quantity }} {{ p.unit }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ p.min_threshold ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ p.expiration ? new Date(p.expiration).toLocaleDateString('fr-FR') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ p.price != null ? Number(p.price).toFixed(2) + ' €' : '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ p.supplier || '—' }}</td>
                                <td class="px-4 py-3"><StatusBadge :label="p.status.label" :cls="p.status.cls" /></td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button @click="openOut(p)"
                                            class="rounded border border-brand-accent px-2 py-1 text-xs font-semibold text-brand-accent hover:bg-brand-accent hover:text-white">
                                            Sortie
                                        </button>
                                        <SecondaryButton @click="openEdit(p)" class="!px-2 !py-1 text-xs">Modifier</SecondaryButton>
                                        <DangerButton @click="destroy(p)" class="!px-2 !py-1 text-xs">Suppr</DangerButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mouvements de stock récents -->
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-gray-100">
                        Mouvements récents
                    </h3>
                    <div v-if="!recentMovements.length" class="text-sm italic text-gray-500">
                        Aucun mouvement enregistré.
                    </div>
                    <table v-else class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-3 py-2 text-left">Date</th>
                                <th class="px-3 py-2 text-left">Produit</th>
                                <th class="px-3 py-2 text-left">Type</th>
                                <th class="px-3 py-2 text-left">Quantité</th>
                                <th class="px-3 py-2 text-left">Motif</th>
                                <th class="px-3 py-2 text-left">Auteur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="m in recentMovements" :key="m.id">
                                <td class="px-3 py-2">{{ new Date(m.date).toLocaleDateString('fr-FR') }}</td>
                                <td class="px-3 py-2 font-medium">{{ m.product?.name }}</td>
                                <td class="px-3 py-2">
                                    <StatusBadge v-if="m.type === 'in'" label="Entrée" cls="ok" />
                                    <StatusBadge v-else label="Sortie" cls="warn" />
                                </td>
                                <td class="px-3 py-2">{{ m.quantity }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ m.reason }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ m.user || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal création/édition produit -->
        <DataModal :show="showModal" :title="editingId ? 'Modifier le produit' : 'Nouveau produit'" @close="showModal = false">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel value="Nom du produit *" />
                    <TextInput v-model="form.name" required class="mt-1 block w-full" />
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Catégorie" />
                        <select v-model="form.category"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                            <option value="">—</option>
                            <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                        </select>
                    </div>
                    <div>
                        <InputLabel value="Fournisseur" />
                        <TextInput v-model="form.supplier" class="mt-1 block w-full" />
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <InputLabel value="Quantité *" />
                        <TextInput v-model="form.quantity" type="number" step="0.01" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Unité" />
                        <TextInput v-model="form.unit" placeholder="kg, L, pcs..." class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Seuil mini" />
                        <TextInput v-model="form.min_threshold" type="number" step="0.01" class="mt-1 block w-full" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Péremption" />
                        <TextInput v-model="form.expiration" type="date" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Prix unitaire (€)" />
                        <TextInput v-model="form.price" type="number" step="0.01" class="mt-1 block w-full" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                </div>
            </form>
        </DataModal>

        <!-- Modal sortie de stock -->
        <DataModal :show="showOutModal" :title="outProduct ? `Sortie de stock — ${outProduct.name}` : ''" @close="showOutModal = false">
            <form @submit.prevent="submitOut" class="space-y-4">
                <div class="rounded bg-gray-100 p-2 text-sm dark:bg-gray-900">
                    Stock actuel : <strong>{{ outProduct?.quantity }} {{ outProduct?.unit }}</strong>
                </div>
                <div>
                    <InputLabel value="Quantité à sortir *" />
                    <TextInput v-model="outForm.quantity" type="number" step="0.01" required class="mt-1 block w-full" />
                    <InputError :message="outForm.errors.quantity" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Motif *" />
                    <select v-model="outForm.reason" required
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        <option v-for="r in OUT_REASONS" :key="r" :value="r">{{ r }}</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <SecondaryButton @click="showOutModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="outForm.processing">Enregistrer la sortie</PrimaryButton>
                </div>
            </form>
        </DataModal>
    </AuthenticatedLayout>
</template>
