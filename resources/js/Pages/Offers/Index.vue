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
    offers: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const showModal = ref(false);
const statusFilter = ref(props.filters.status || '');

const form = useForm({
    title: '',
    department: '',
    location: '',
    contract_type: '',
    description: '',
    requirements: '',
    salary_range: '',
    status: 'draft',
});

const statusOptions = [
    { value: '', label: 'Tous les statuts' },
    { value: 'draft', label: 'Brouillon' },
    { value: 'active', label: 'Active' },
    { value: 'archived', label: 'Archivée' },
];

const contractTypes = ['CDI', 'CDD', 'Stage', 'Alternance', 'Interim'];

const statusColor = (status) => {
    const map = { draft: 'warn', active: 'ok', archived: 'info' };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const map = { draft: 'Brouillon', active: 'Active', archived: 'Archivée' };
    return map[status] || status;
};

watch(statusFilter, () => {
    router.get(route('offers.index'), {
        status: statusFilter.value || undefined,
    }, { preserveState: true, replace: true });
});

const submit = () => {
    form.post(route('offers.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const destroy = (id) => {
    if (confirm('Supprimer cette offre ?')) {
        router.post(route('offers.destroy', id));
    }
};
</script>

<template>
    <Head title="Offres d'emploi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">Offres d'emploi</h2>
                <PrimaryButton @click="showModal = true">+ Créer une offre</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filtre -->
                <div class="mb-6">
                    <select v-model="statusFilter"
                        class="rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                </div>

                <!-- Grille d'offres -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="o in offers.data" :key="o.id"
                        class="rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800 transition hover:shadow-md">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <Link :href="route('offers.show', o.id)" class="text-base font-semibold text-gray-900 dark:text-gray-100 hover:text-brand-primary">
                                    {{ o.title }}
                                </Link>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span v-if="o.department">{{ o.department }}</span>
                                    <span v-if="o.department && o.location"> - </span>
                                    <span v-if="o.location">{{ o.location }}</span>
                                </div>
                            </div>
                            <StatusBadge :label="statusLabel(o.status)" :cls="statusColor(o.status)" />
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                            <span v-if="o.contract_type" class="rounded-full bg-brand-lavender px-2 py-0.5 text-violet-800 font-medium">{{ o.contract_type }}</span>
                            <span v-if="o.salary_range">{{ o.salary_range }}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">{{ o.description }}</p>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">{{ new Date(o.created_at).toLocaleDateString('fr-FR') }}</span>
                            <div class="flex gap-2">
                                <Link :href="route('offers.show', o.id)" class="text-brand-primary hover:underline">Voir</Link>
                                <button @click="destroy(o.id)" class="text-pink-600 hover:underline">Supprimer</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="!offers.data.length" class="rounded-xl bg-white p-8 shadow-soft text-center dark:bg-gray-800 mt-4">
                    <p class="text-sm italic text-gray-500">Aucune offre trouvée</p>
                </div>

                <!-- Pagination -->
                <div v-if="offers.links && offers.links.length > 3" class="mt-6 flex justify-center gap-1">
                    <template v-for="link in offers.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            class="rounded px-3 py-1 text-sm transition"
                            :class="link.active ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-brand-tertiary'"
                            v-html="link.label" />
                        <span v-else class="rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal création -->
        <Modal :show="showModal" @close="showModal = false" max-width="2xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Créer une offre d'emploi</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <InputLabel value="Titre du poste *" />
                            <TextInput v-model="form.title" class="mt-1 w-full" required />
                            <InputError :message="form.errors.title" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Département" />
                            <TextInput v-model="form.department" class="mt-1 w-full" />
                        </div>
                        <div>
                            <InputLabel value="Localisation" />
                            <TextInput v-model="form.location" class="mt-1 w-full" />
                        </div>
                        <div>
                            <InputLabel value="Type de contrat" />
                            <select v-model="form.contract_type"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Sélectionner</option>
                                <option v-for="ct in contractTypes" :key="ct" :value="ct">{{ ct }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Fourchette salariale" />
                            <TextInput v-model="form.salary_range" class="mt-1 w-full" placeholder="Ex: 35-45k EUR" />
                        </div>
                    </div>
                    <div>
                        <InputLabel value="Description *" />
                        <textarea v-model="form.description" rows="4" required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        <InputError :message="form.errors.description" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Prérequis" />
                        <textarea v-model="form.requirements" rows="3"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Créer l'offre</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
