<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    reviews: { type: Array, default: () => [] },
    employees: { type: Array, default: () => [] },
    defaultYear: { type: Number, required: true },
    can: { type: Object, default: () => ({ create: false }) },
});

const page = usePage();
const isManager = computed(() => !!page.props.auth.isManager);

const form = useForm({
    employee_id: '',
    year: props.defaultYear,
    scheduled_for: '',
});

const showForm = ref(false);

const submit = () => {
    form.post(route('reviews.store'), {
        onSuccess: () => {
            form.reset();
            form.year = props.defaultYear;
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
                    {{ showForm ? 'Annuler' : 'Planifier un entretien' }}
                </PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Formulaire création (managers) -->
                <div v-if="showForm && can.create" class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-gray-100">
                        Planifier un nouvel entretien
                    </h3>
                    <form class="grid grid-cols-1 gap-4 sm:grid-cols-3" @submit.prevent="submit">
                        <div>
                            <InputLabel for="employee_id" value="Salarié" />
                            <select id="employee_id" v-model="form.employee_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="" disabled>— choisir —</option>
                                <option v-for="e in employees" :key="e.id" :value="e.id">
                                    {{ e.name }}<span v-if="e.position"> — {{ e.position }}</span>
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.employee_id" />
                            <p v-if="!employees.length" class="mt-2 text-xs text-gray-500">
                                Aucun salarié rattaché. Ajoutez des salariés et définissez leur manager pour commencer.
                            </p>
                        </div>
                        <div>
                            <InputLabel for="year" value="Année" />
                            <TextInput id="year" v-model="form.year" type="number" min="2000" max="2100" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.year" />
                        </div>
                        <div>
                            <InputLabel for="scheduled_for" value="Date prévue" />
                            <TextInput id="scheduled_for" v-model="form.scheduled_for" type="date" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="form.errors.scheduled_for" />
                        </div>
                        <div class="sm:col-span-3 flex justify-end gap-2">
                            <SecondaryButton type="button" @click="showForm = false">Annuler</SecondaryButton>
                            <PrimaryButton :disabled="form.processing">Planifier</PrimaryButton>
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
