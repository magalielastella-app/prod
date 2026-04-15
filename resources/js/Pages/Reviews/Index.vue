<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    reviews: { type: Array, default: () => [] },
    employees: { type: Array, default: () => [] },
    potentialManagers: { type: Array, default: () => [] },
    defaultYear: { type: Number, required: true },
    can: { type: Object, default: () => ({ create: false, pickManager: false }) },
});

const page = usePage();
const isManager = computed(() => !!page.props.auth.isManager);

const blankRow = () => ({ employee_id: '', manager_id: '', scheduled_for: '' });

const form = useForm({
    year: props.defaultYear,
    assignments: [blankRow()],
});

const showForm = ref(false);

const addRow = () => form.assignments.push(blankRow());
const removeRow = (i) => {
    if (form.assignments.length > 1) form.assignments.splice(i, 1);
};

// Les salariés déjà sélectionnés dans le formulaire — pour éviter les doublons
// dans les autres dropdowns (mais on laisse voir tous les choix).
const submit = () => {
    form.post(route('reviews.store'), {
        onSuccess: () => {
            form.reset();
            form.year = props.defaultYear;
            form.assignments = [blankRow()];
            showForm.value = false;
        },
    });
};

const statusColor = (status) => {
    if (status === 'signed') return 'ok';
    if (status === 'completed') return 'info';
    if (status === 'ready_for_manager') return 'warn';
    if (status === 'scheduled') return 'info';
    return 'warn';
};

const destroy = (id) => {
    if (!confirm('Supprimer cet entretien ?')) return;
    useForm({}).delete(route('reviews.destroy', id));
};
</script>

<template>
    <Head title="Entretiens annuels" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Entretiens annuels
                </h2>
                <PrimaryButton v-if="can.create" @click="showForm = !showForm">
                    {{ showForm ? 'Annuler' : 'Planifier des entretiens' }}
                </PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Formulaire planification (multi-lignes) -->
                <div v-if="showForm && can.create" class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-gray-100">
                        Planifier un ou plusieurs entretiens
                    </h3>

                    <form @submit.prevent="submit">
                        <!-- Année commune -->
                        <div class="mb-4 max-w-xs">
                            <InputLabel for="year" value="Année" />
                            <TextInput id="year" v-model="form.year" type="number" min="2000" max="2100"
                                class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.year" />
                        </div>

                        <!-- Lignes d'assignation -->
                        <div class="space-y-3">
                            <div v-for="(row, i) in form.assignments" :key="i"
                                class="grid grid-cols-1 gap-3 rounded border border-gray-200 p-3 sm:grid-cols-12 dark:border-gray-700">
                                <!-- Salarié -->
                                <div :class="can.pickManager ? 'sm:col-span-5' : 'sm:col-span-7'">
                                    <InputLabel :for="`employee_${i}`" value="Salarié" />
                                    <select :id="`employee_${i}`" v-model="row.employee_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="" disabled>— choisir —</option>
                                        <option v-for="e in employees" :key="e.id" :value="e.id">
                                            {{ e.name }}<span v-if="e.position"> — {{ e.position }}</span>
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors[`assignments.${i}.employee_id`]" />
                                </div>

                                <!-- Manager (admin seulement) -->
                                <div v-if="can.pickManager" class="sm:col-span-4">
                                    <InputLabel :for="`manager_${i}`" value="Manager qui conduira l'entretien" />
                                    <select :id="`manager_${i}`" v-model="row.manager_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="">— moi-même —</option>
                                        <option v-for="m in potentialManagers" :key="m.id" :value="m.id"
                                            :disabled="m.id === row.employee_id">
                                            {{ m.name }}<span v-if="m.position"> — {{ m.position }}</span>
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors[`assignments.${i}.manager_id`]" />
                                </div>

                                <!-- Date -->
                                <div class="sm:col-span-2">
                                    <InputLabel :for="`date_${i}`" value="Date prévue" />
                                    <TextInput :id="`date_${i}`" v-model="row.scheduled_for" type="date"
                                        class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors[`assignments.${i}.scheduled_for`]" />
                                </div>

                                <!-- Retirer la ligne -->
                                <div class="sm:col-span-1 flex items-end">
                                    <button type="button"
                                        class="w-full rounded border border-red-200 px-2 py-2 text-xs text-red-600 hover:bg-red-50 disabled:opacity-40"
                                        :disabled="form.assignments.length === 1"
                                        @click="removeRow(i)">
                                        Retirer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <SecondaryButton type="button" @click="addRow">
                                + Ajouter une personne
                            </SecondaryButton>
                        </div>

                        <p v-if="!employees.length" class="mt-3 text-xs text-gray-500">
                            Aucun salarié enregistré. Ajoutez d'abord les membres de l'équipe dans l'onglet
                            « Équipe ».
                        </p>

                        <div class="mt-5 flex justify-end gap-2">
                            <SecondaryButton type="button" @click="showForm = false">Annuler</SecondaryButton>
                            <PrimaryButton :disabled="form.processing">
                                Planifier ({{ form.assignments.length }})
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Liste -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-4 py-3">Année</th>
                                <th class="px-4 py-3">Salarié</th>
                                <th class="px-4 py-3">Manager</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="r in reviews" :key="r.id" class="text-sm text-gray-700 dark:text-gray-200">
                                <td class="px-4 py-3 font-medium">{{ r.year }}</td>
                                <td class="px-4 py-3">
                                    <div>{{ r.employee?.name }}</div>
                                    <div class="text-xs text-gray-500">{{ r.employee?.position }}</div>
                                </td>
                                <td class="px-4 py-3">{{ r.manager?.name || '—' }}</td>
                                <td class="px-4 py-3">{{ r.scheduled_for || '—' }}</td>
                                <td class="px-4 py-3">
                                    <StatusBadge :label="r.status_label" :cls="statusColor(r.status)" />
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <Link :href="route('reviews.show', r.id)"
                                        class="text-brand-primary hover:underline">Ouvrir</Link>
                                    <a :href="route('reviews.pdf', r.id)"
                                        class="text-brand-primary hover:underline">PDF</a>
                                    <button v-if="isManager && !r.signed"
                                        class="text-red-600 hover:underline" @click="destroy(r.id)">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!reviews.length">
                                <td colspan="6" class="px-4 py-10 text-center text-sm italic text-gray-500">
                                    Aucun entretien pour le moment.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
