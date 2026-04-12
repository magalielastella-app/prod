<script setup>
import { computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    suppliers: { type: Array, required: true },
    products: { type: Array, required: true },
    preselectedSupplier: { type: Number, default: 0 },
    cadencierBySupplier: { type: Object, default: () => ({}) },
});

const todayISO = new Date().toISOString().slice(0, 10);
const form = useForm({
    supplier_id: props.preselectedSupplier || props.suppliers[0]?.id || '',
    invoice_number: '',
    date: todayISO,
    notes: '',
    items: [emptyLine()],
});

function emptyLine() {
    return { product_id: '', label: '', quantity: 1, unit: '', unit_price: 0 };
}
function addLine() { form.items.push(emptyLine()); }
function removeLine(i) { form.items.splice(i, 1); }

// Quand le fournisseur change, on propose de pré-remplir depuis son cadencier.
function loadCadencier() {
    const lines = props.cadencierBySupplier[form.supplier_id] || [];
    if (!lines.length) return;
    if (!confirm(`Charger les ${lines.length} produits du cadencier dans la facture ?`)) return;
    form.items = lines.map(l => ({
        product_id: l.product_id || '',
        label: l.name,
        quantity: l.usual_quantity ?? 1,
        unit: l.unit || '',
        unit_price: l.price ?? 0,
    }));
}

// Si on sélectionne un produit de l'inventaire dans une ligne, on pré-remplit le label/unité.
function onProductChange(i) {
    const pid = form.items[i].product_id;
    if (!pid) return;
    const p = props.products.find(pp => pp.id == pid);
    if (p) {
        if (!form.items[i].label) form.items[i].label = p.name;
        if (!form.items[i].unit) form.items[i].unit = p.unit || '';
    }
}

const totalAmount = computed(() =>
    form.items.reduce((sum, l) => sum + (Number(l.quantity) || 0) * (Number(l.unit_price) || 0), 0)
);

function submit() {
    form.post(route('invoices.store'));
}
</script>

<template>
    <Head title="Nouvelle facture" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Nouvelle facture fournisseur
                </h2>
                <Link :href="route('suppliers.index')" class="text-sm text-gray-500 underline">← Retour</Link>
            </div>
        </template>

        <div class="py-8">
            <form @submit.prevent="submit" class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <!-- En-tête facture -->
                <div class="grid grid-cols-1 gap-4 rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800 md:grid-cols-4">
                    <div>
                        <InputLabel value="Fournisseur *" />
                        <select v-model="form.supplier_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                            <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <button type="button" @click="loadCadencier" class="mt-1 text-xs text-brand-accent underline">
                            Charger le cadencier
                        </button>
                    </div>
                    <div>
                        <InputLabel value="Numéro de facture" />
                        <TextInput v-model="form.invoice_number" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Date *" />
                        <TextInput v-model="form.date" type="date" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Total" />
                        <div class="mt-1 rounded-md bg-gray-100 px-3 py-2 text-right text-lg font-bold dark:bg-gray-900 dark:text-gray-100">
                            {{ totalAmount.toFixed(2) }} €
                        </div>
                    </div>
                </div>

                <!-- Lignes -->
                <div class="overflow-x-auto rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-3 py-2">Produit (inventaire)</th>
                                <th class="px-3 py-2">Libellé *</th>
                                <th class="px-3 py-2">Qté *</th>
                                <th class="px-3 py-2">Unité</th>
                                <th class="px-3 py-2">PU HT</th>
                                <th class="px-3 py-2">Total ligne</th>
                                <th class="px-3 py-2 text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="(line, i) in form.items" :key="i">
                                <td class="px-2 py-2">
                                    <select v-model="line.product_id" @change="onProductChange(i)"
                                        class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                        <option value="">— libre —</option>
                                        <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                                    </select>
                                </td>
                                <td class="px-2 py-2">
                                    <TextInput v-model="line.label" required class="w-full text-xs" />
                                </td>
                                <td class="px-2 py-2">
                                    <TextInput v-model="line.quantity" type="number" step="0.01" required class="w-20 text-xs" />
                                </td>
                                <td class="px-2 py-2">
                                    <TextInput v-model="line.unit" class="w-16 text-xs" />
                                </td>
                                <td class="px-2 py-2">
                                    <TextInput v-model="line.unit_price" type="number" step="0.01" class="w-24 text-xs" />
                                </td>
                                <td class="px-2 py-2 font-medium">
                                    {{ (Number(line.quantity || 0) * Number(line.unit_price || 0)).toFixed(2) }} €
                                </td>
                                <td class="px-2 py-2 text-right">
                                    <DangerButton v-if="form.items.length > 1" @click.prevent="removeLine(i)" class="!px-2 !py-1 text-xs">×</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="p-3">
                        <SecondaryButton type="button" @click="addLine">+ Ajouter une ligne</SecondaryButton>
                    </div>
                </div>

                <InputError :message="form.errors.items" class="text-sm" />

                <div>
                    <InputLabel value="Notes" />
                    <textarea v-model="form.notes" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                </div>

                <div class="rounded-lg bg-brand-tertiary p-3 text-sm text-brand-primary">
                    ℹ Les lignes rattachées à un produit de l'inventaire créent automatiquement
                    une entrée en stock à l'enregistrement.
                </div>

                <div class="flex justify-end gap-2">
                    <Link :href="route('suppliers.index')" class="rounded border border-gray-300 px-4 py-2 text-sm">Annuler</Link>
                    <PrimaryButton :disabled="form.processing">Enregistrer la facture</PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
