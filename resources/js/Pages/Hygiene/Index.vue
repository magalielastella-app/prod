<script setup>
import { ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataModal from '@/Components/DataModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    date: { type: String, required: true },
    zones: { type: Array, required: true },
    frequencies: { type: Array, required: true },
    temperatures: { type: Array, required: true },
    cleaningTasks: { type: Array, required: true },
    deliveries: { type: Array, required: true },
});

const currentDate = ref(props.date);
watch(currentDate, (v) => {
    router.get(route('hygiene.index'), { date: v }, { preserveState: false, replace: true });
});

// ---------- Températures ----------
const showTempModal = ref(false);
const tempForm = useForm({
    date: props.date,
    zone: props.zones[0] || '',
    temp: null,
    time: new Date().toTimeString().slice(0, 5),
    agent: '',
});
function openTempModal() {
    tempForm.clearErrors();
    tempForm.date = currentDate.value;
    tempForm.time = new Date().toTimeString().slice(0, 5);
    showTempModal.value = true;
}
function submitTemp() {
    tempForm.post(route('temperatures.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showTempModal.value = false;
            tempForm.reset('temp', 'agent');
        },
    });
}
function deleteTemp(t) {
    if (!confirm('Supprimer ce relevé ?')) return;
    router.delete(route('temperatures.destroy', t.id), { preserveScroll: true });
}

// ---------- Nettoyage ----------
const showCleanModal = ref(false);
const cleanForm = useForm({ zone: '', frequency: 'Quotidien', agent: '' });
function submitClean() {
    cleanForm.post(route('cleaning-tasks.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCleanModal.value = false;
            cleanForm.reset();
            cleanForm.frequency = 'Quotidien';
        },
    });
}
function markDone(task) {
    const agent = prompt("Nom de l'agent ayant effectué le nettoyage :");
    if (agent === null || agent.trim() === '') return;
    router.post(route('cleaning-tasks.done', task.id), { agent }, { preserveScroll: true });
}
function deleteTask(task) {
    if (!confirm('Supprimer cette tâche ?')) return;
    router.delete(route('cleaning-tasks.destroy', task.id), { preserveScroll: true });
}

// ---------- Livraisons ----------
const showDelivModal = ref(false);
const delivForm = useForm({
    date: props.date,
    supplier: '', product: '', quantity: null, unit: '',
    temp_delivery: null, dlc: '', compliant: true,
});
function openDelivModal() {
    delivForm.clearErrors();
    delivForm.date = currentDate.value;
    showDelivModal.value = true;
}
function submitDeliv() {
    delivForm.post(route('deliveries.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showDelivModal.value = false;
            delivForm.reset();
            delivForm.compliant = true;
            delivForm.date = currentDate.value;
        },
    });
}
function deleteDeliv(d) {
    if (!confirm('Supprimer cette réception ?')) return;
    router.delete(route('deliveries.destroy', d.id), { preserveScroll: true });
}

function fmtDate(d) {
    return d ? new Date(d).toLocaleDateString('fr-FR') : '—';
}
</script>

<template>
    <Head title="Hygiène" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Module Hygiène (HACCP)
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                    <label class="text-sm text-gray-600 dark:text-gray-300">Date :</label>
                    <TextInput v-model="currentDate" type="date" />
                    <div class="ml-auto flex flex-wrap gap-2">
                        <PrimaryButton @click="openTempModal">+ Relevé température</PrimaryButton>
                        <PrimaryButton @click="showCleanModal = true">+ Tâche nettoyage</PrimaryButton>
                        <PrimaryButton @click="openDelivModal">+ Réception</PrimaryButton>
                    </div>
                </div>

                <!-- Températures -->
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-gray-100">
                        Relevés de température ({{ new Date(currentDate).toLocaleDateString('fr-FR') }})
                    </h3>
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-3 py-2 text-left">Zone</th>
                                <th class="px-3 py-2 text-left">Temp.</th>
                                <th class="px-3 py-2 text-left">Heure</th>
                                <th class="px-3 py-2 text-left">Conformité</th>
                                <th class="px-3 py-2 text-left">Agent</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="!temperatures.length">
                                <td colspan="6" class="px-3 py-6 text-center italic text-gray-500">Aucun relevé pour cette date.</td>
                            </tr>
                            <tr v-for="t in temperatures" :key="t.id">
                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-gray-100">{{ t.zone }}</td>
                                <td class="px-3 py-2 font-bold">{{ t.temp }}°C</td>
                                <td class="px-3 py-2">{{ t.time?.slice(0, 5) }}</td>
                                <td class="px-3 py-2">
                                    <StatusBadge v-if="t.compliant" label="Conforme" cls="ok" />
                                    <StatusBadge v-else label="Non conforme" cls="danger" />
                                </td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ t.agent || '—' }}</td>
                                <td class="px-3 py-2 text-right">
                                    <DangerButton @click="deleteTemp(t)" class="!px-2 !py-1 text-xs">Suppr</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Plan de nettoyage -->
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-gray-100">Plan de nettoyage</h3>
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-3 py-2 text-left">Zone</th>
                                <th class="px-3 py-2 text-left">Fréquence</th>
                                <th class="px-3 py-2 text-left">Dernier nettoyage</th>
                                <th class="px-3 py-2 text-left">Agent</th>
                                <th class="px-3 py-2 text-left">Statut</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="!cleaningTasks.length">
                                <td colspan="6" class="px-3 py-6 text-center italic text-gray-500">Aucune tâche.</td>
                            </tr>
                            <tr v-for="t in cleaningTasks" :key="t.id">
                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-gray-100">{{ t.zone }}</td>
                                <td class="px-3 py-2">{{ t.frequency }}</td>
                                <td class="px-3 py-2">{{ fmtDate(t.last_done) }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ t.agent || '—' }}</td>
                                <td class="px-3 py-2"><StatusBadge :label="t.status.label" :cls="t.status.cls" /></td>
                                <td class="px-3 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <SecondaryButton @click="markDone(t)" class="!px-2 !py-1 text-xs">Marquer fait</SecondaryButton>
                                        <DangerButton @click="deleteTask(t)" class="!px-2 !py-1 text-xs">Suppr</DangerButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Livraisons -->
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-gray-100">
                        Réception marchandises ({{ new Date(currentDate).toLocaleDateString('fr-FR') }})
                    </h3>
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-3 py-2 text-left">Fournisseur</th>
                                <th class="px-3 py-2 text-left">Produit</th>
                                <th class="px-3 py-2 text-left">Quantité</th>
                                <th class="px-3 py-2 text-left">Temp. livr.</th>
                                <th class="px-3 py-2 text-left">DLC</th>
                                <th class="px-3 py-2 text-left">Conformité</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="!deliveries.length">
                                <td colspan="7" class="px-3 py-6 text-center italic text-gray-500">Aucune réception enregistrée.</td>
                            </tr>
                            <tr v-for="d in deliveries" :key="d.id">
                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-gray-100">{{ d.supplier }}</td>
                                <td class="px-3 py-2">{{ d.product }}</td>
                                <td class="px-3 py-2">{{ d.quantity }} {{ d.unit }}</td>
                                <td class="px-3 py-2">{{ d.temp_delivery != null ? d.temp_delivery + '°C' : '—' }}</td>
                                <td class="px-3 py-2">{{ fmtDate(d.dlc) }}</td>
                                <td class="px-3 py-2">
                                    <StatusBadge v-if="d.compliant" label="Conforme" cls="ok" />
                                    <StatusBadge v-else label="Non conforme" cls="danger" />
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <DangerButton @click="deleteDeliv(d)" class="!px-2 !py-1 text-xs">Suppr</DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal température -->
        <DataModal :show="showTempModal" title="Relevé de température" @close="showTempModal = false">
            <form @submit.prevent="submitTemp" class="space-y-4">
                <div>
                    <InputLabel value="Zone *" />
                    <select v-model="tempForm.zone" required
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        <option v-for="z in zones" :key="z" :value="z">{{ z }}</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Température (°C) *" />
                        <TextInput v-model="tempForm.temp" type="number" step="0.1" required class="mt-1 block w-full" />
                        <InputError :message="tempForm.errors.temp" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Heure *" />
                        <TextInput v-model="tempForm.time" type="time" required class="mt-1 block w-full" />
                    </div>
                </div>
                <div>
                    <InputLabel value="Agent (qui a relevé) *" />
                    <TextInput v-model="tempForm.agent" required class="mt-1 block w-full" />
                    <InputError :message="tempForm.errors.agent" class="mt-1" />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <SecondaryButton @click="showTempModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="tempForm.processing">Enregistrer</PrimaryButton>
                </div>
            </form>
        </DataModal>

        <!-- Modal nettoyage -->
        <DataModal :show="showCleanModal" title="Tâche de nettoyage" @close="showCleanModal = false">
            <form @submit.prevent="submitClean" class="space-y-4">
                <div>
                    <InputLabel value="Zone / Équipement *" />
                    <TextInput v-model="cleanForm.zone" required placeholder="Ex: Plan de travail cuisine" class="mt-1 block w-full" />
                    <InputError :message="cleanForm.errors.zone" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Fréquence *" />
                    <select v-model="cleanForm.frequency" required
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        <option v-for="f in frequencies" :key="f" :value="f">{{ f }}</option>
                    </select>
                </div>
                <div>
                    <InputLabel value="Responsable" />
                    <TextInput v-model="cleanForm.agent" class="mt-1 block w-full" />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <SecondaryButton @click="showCleanModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="cleanForm.processing">Enregistrer</PrimaryButton>
                </div>
            </form>
        </DataModal>

        <!-- Modal réception -->
        <DataModal :show="showDelivModal" title="Réception marchandise" @close="showDelivModal = false">
            <form @submit.prevent="submitDeliv" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Fournisseur *" />
                        <TextInput v-model="delivForm.supplier" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Produit *" />
                        <TextInput v-model="delivForm.product" required class="mt-1 block w-full" />
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <InputLabel value="Quantité *" />
                        <TextInput v-model="delivForm.quantity" type="number" step="0.01" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Unité" />
                        <TextInput v-model="delivForm.unit" placeholder="kg, L, pcs" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Temp. livraison (°C)" />
                        <TextInput v-model="delivForm.temp_delivery" type="number" step="0.1" class="mt-1 block w-full" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="DLC / DDM" />
                        <TextInput v-model="delivForm.dlc" type="date" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Conformité" />
                        <select v-model="delivForm.compliant"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                            <option :value="true">Conforme</option>
                            <option :value="false">Non conforme</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <SecondaryButton @click="showDelivModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="delivForm.processing">Enregistrer</PrimaryButton>
                </div>
            </form>
        </DataModal>
    </AuthenticatedLayout>
</template>
