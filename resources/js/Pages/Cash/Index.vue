<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataModal from '@/Components/DataModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    month: { type: String, required: true },
    sheets: { type: Array, required: true },
    paymentFields: { type: Object, required: true },
    summary: { type: Object, required: true },
});

const currentMonth = ref(props.month);
watch(currentMonth, (v) => {
    router.get(route('cash.index'), { month: v }, { preserveState: false, replace: true });
});

// Formulaire création/édition
const showModal = ref(false);
const editingId = ref(null);
const form = useForm({
    date: new Date().toISOString().slice(0, 10),
    ca: 0,
    ca_plateforme: 0,
    cb: 0,
    cb_sans_contact: 0,
    espece: 0,
    ticket_restaurant: 0,
    borne: 0,
    notes: '',
});

// Total attendu (somme des modes de règlement)
const paymentTotal = computed(() =>
    (Number(form.ca_plateforme) || 0)
    + (Number(form.cb) || 0)
    + (Number(form.cb_sans_contact) || 0)
    + (Number(form.espece) || 0)
    + (Number(form.ticket_restaurant) || 0)
    + (Number(form.borne) || 0)
);
const difference = computed(() => ((Number(form.ca) || 0) - paymentTotal.value).toFixed(2));

function openCreate() {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.date = new Date().toISOString().slice(0, 10);
    showModal.value = true;
}
function openEdit(s) {
    editingId.value = s.id;
    form.clearErrors();
    Object.assign(form, {
        date: s.date,
        ca: s.ca,
        ca_plateforme: s.ca_plateforme,
        cb: s.cb,
        cb_sans_contact: s.cb_sans_contact,
        espece: s.espece,
        ticket_restaurant: s.ticket_restaurant,
        borne: s.borne,
        notes: s.notes || '',
    });
    showModal.value = true;
}
function submit() {
    const opts = { preserveScroll: true, onSuccess: () => (showModal.value = false) };
    if (editingId.value) {
        form.put(route('cash.update', editingId.value), opts);
    } else {
        form.post(route('cash.store'), opts);
    }
}
function destroy(s) {
    if (!confirm(`Supprimer la feuille du ${new Date(s.date).toLocaleDateString('fr-FR')} ?`)) return;
    router.delete(route('cash.destroy', s.id), { preserveScroll: true });
}

const monthLabel = computed(() => {
    const [y, m] = currentMonth.value.split('-');
    return new Date(y, m - 1, 1).toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
});
</script>

<template>
    <Head title="Caisse" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Caisse — feuille quotidienne
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                    <label class="text-sm text-gray-600 dark:text-gray-300">Mois :</label>
                    <TextInput v-model="currentMonth" type="month" />
                    <span class="text-sm text-gray-500">{{ monthLabel }}</span>
                    <PrimaryButton class="ml-auto" @click="openCreate">+ Feuille du jour</PrimaryButton>
                </div>

                <!-- Synthèse mois -->
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-7">
                    <div class="rounded-lg bg-brand-primary p-4 text-white shadow-sm">
                        <div class="text-xs font-medium uppercase opacity-90">CA total</div>
                        <div class="mt-1 text-2xl font-bold">{{ summary.ca_total.toFixed(2) }} €</div>
                    </div>
                    <div v-for="(label, key) in paymentFields" :key="key" class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="text-xs font-medium uppercase text-gray-500">{{ label }}</div>
                        <div class="mt-1 text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ Number(summary[key]).toFixed(2) }} €
                        </div>
                    </div>
                </div>

                <!-- Table des feuilles -->
                <div class="overflow-x-auto rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-3 py-3 text-left">Date</th>
                                <th class="px-3 py-3">CA</th>
                                <th v-for="(label, key) in paymentFields" :key="key" class="px-3 py-3">{{ label }}</th>
                                <th class="px-3 py-3">Écart</th>
                                <th class="px-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="!sheets.length">
                                <td colspan="10" class="px-4 py-8 text-center italic text-gray-500">
                                    Aucune feuille pour ce mois.
                                </td>
                            </tr>
                            <tr v-for="s in sheets" :key="s.id" class="text-right hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                <td class="px-3 py-2 text-left font-medium">{{ new Date(s.date).toLocaleDateString('fr-FR') }}</td>
                                <td class="px-3 py-2 font-semibold">{{ Number(s.ca).toFixed(2) }}</td>
                                <td v-for="(label, key) in paymentFields" :key="key" class="px-3 py-2">{{ Number(s[key]).toFixed(2) }}</td>
                                <td class="px-3 py-2">
                                    <span :class="Math.abs(s.difference) < 0.01 ? 'text-emerald-600' : 'text-red-600'">
                                        {{ Number(s.difference).toFixed(2) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <SecondaryButton @click="openEdit(s)" class="!px-2 !py-1 text-xs">Modifier</SecondaryButton>
                                        <DangerButton @click="destroy(s)" class="!px-2 !py-1 text-xs">Suppr</DangerButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal feuille de caisse -->
        <DataModal :show="showModal" :title="editingId ? 'Modifier la feuille' : 'Nouvelle feuille de caisse'"
            max-width="3xl" @close="showModal = false">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel value="Date *" />
                    <TextInput v-model="form.date" type="date" required class="mt-1 block w-full" />
                    <InputError :message="form.errors.date" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="CA total du jour (€) *" />
                    <TextInput v-model="form.ca" type="number" step="0.01" required class="mt-1 block w-full text-lg font-bold" />
                    <InputError :message="form.errors.ca" class="mt-1" />
                </div>
                <div class="rounded-md border border-gray-200 p-3 dark:border-gray-700">
                    <div class="mb-2 text-xs uppercase text-gray-500">Décomposition par mode de règlement</div>
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                        <div>
                            <InputLabel value="CA plateforme" />
                            <TextInput v-model="form.ca_plateforme" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="CB" />
                            <TextInput v-model="form.cb" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="CB sans contact" />
                            <TextInput v-model="form.cb_sans_contact" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Espèces" />
                            <TextInput v-model="form.espece" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Ticket restaurant" />
                            <TextInput v-model="form.ticket_restaurant" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Borne" />
                            <TextInput v-model="form.borne" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between rounded bg-brand-tertiary/60 px-3 py-2 text-sm">
                        <span>Somme des règlements : <strong>{{ paymentTotal.toFixed(2) }} €</strong></span>
                        <span :class="Math.abs(difference) < 0.01 ? 'text-emerald-700' : 'text-red-700'">
                            Écart : <strong>{{ difference }} €</strong>
                        </span>
                    </div>
                </div>
                <div>
                    <InputLabel value="Notes" />
                    <textarea v-model="form.notes" rows="2"
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
