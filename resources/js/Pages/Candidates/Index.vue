<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    candidates: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const showModal = ref(false);
const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    city: '',
    source: '',
    notes: '',
});

const statuses = [
    { value: '', label: 'Tous les statuts' },
    { value: 'new', label: 'Nouveau' },
    { value: 'screening', label: 'Tri' },
    { value: 'interview', label: 'Entretien' },
    { value: 'offer', label: 'Offre' },
    { value: 'hired', label: 'Embauché' },
    { value: 'rejected', label: 'Refusé' },
];

const statusColor = (status) => {
    const map = { new: 'info', screening: 'warn', interview: 'warn', offer: 'ok', hired: 'ok', rejected: 'danger' };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const map = { new: 'Nouveau', screening: 'Tri', interview: 'Entretien', offer: 'Offre', hired: 'Embauché', rejected: 'Refusé' };
    return map[status] || status;
};

let searchTimeout = null;
watch([search, statusFilter], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('candidates.index'), {
            search: search.value || undefined,
            status: statusFilter.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

const submit = () => {
    form.post(route('candidates.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const destroy = (id) => {
    if (confirm('Supprimer ce candidat ?')) {
        router.post(route('candidates.destroy', id));
    }
};
</script>

<template>
    <Head title="CVthèque" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">CVthèque</h2>
                <PrimaryButton @click="showModal = true">+ Ajouter un candidat</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filtres -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <TextInput v-model="search" placeholder="Rechercher un candidat..." class="w-full sm:w-80" />
                    <select v-model="statusFilter"
                        class="rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                </div>

                <!-- Tableau -->
                <div class="overflow-hidden rounded-xl bg-white shadow-soft dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-brand-tertiary/50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Téléphone</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="c in candidates.data" :key="c.id" class="hover:bg-brand-tertiary/30 transition">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ c.first_name }} {{ c.last_name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ c.email || '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ c.phone || '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <StatusBadge :label="statusLabel(c.status)" :cls="statusColor(c.status)" />
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ new Date(c.created_at).toLocaleDateString('fr-FR') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm space-x-2">
                                    <Link :href="route('candidates.show', c.id)" class="text-brand-primary hover:underline">Voir</Link>
                                    <button @click="destroy(c.id)" class="text-pink-600 hover:underline">Supprimer</button>
                                </td>
                            </tr>
                            <tr v-if="!candidates.data.length">
                                <td colspan="6" class="px-6 py-8 text-center text-sm italic text-gray-500">Aucun candidat trouvé</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="candidates.links && candidates.links.length > 3" class="mt-4 flex justify-center gap-1">
                    <template v-for="link in candidates.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            class="rounded px-3 py-1 text-sm transition"
                            :class="link.active ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-brand-tertiary'"
                            v-html="link.label" />
                        <span v-else class="rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal ajout -->
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ajouter un candidat</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="first_name" value="Prénom *" />
                            <TextInput id="first_name" v-model="form.first_name" class="mt-1 w-full" required />
                            <InputError :message="form.errors.first_name" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="last_name" value="Nom *" />
                            <TextInput id="last_name" v-model="form.last_name" class="mt-1 w-full" required />
                            <InputError :message="form.errors.last_name" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput id="email" v-model="form.email" type="email" class="mt-1 w-full" />
                            <InputError :message="form.errors.email" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="phone" value="Téléphone" />
                            <TextInput id="phone" v-model="form.phone" class="mt-1 w-full" />
                            <InputError :message="form.errors.phone" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="city" value="Ville" />
                            <TextInput id="city" v-model="form.city" class="mt-1 w-full" />
                            <InputError :message="form.errors.city" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="source" value="Source" />
                            <TextInput id="source" v-model="form.source" class="mt-1 w-full" placeholder="LinkedIn, Indeed..." />
                            <InputError :message="form.errors.source" class="mt-1" />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="notes" value="Notes" />
                        <textarea id="notes" v-model="form.notes" rows="3"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        <InputError :message="form.errors.notes" class="mt-1" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Ajouter</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
