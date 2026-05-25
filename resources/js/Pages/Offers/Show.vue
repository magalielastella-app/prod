<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    offer: { type: Object, required: true },
});

const editing = ref(false);

const form = useForm({
    title: props.offer.title,
    department: props.offer.department || '',
    location: props.offer.location || '',
    contract_type: props.offer.contract_type || '',
    description: props.offer.description,
    requirements: props.offer.requirements || '',
    salary_range: props.offer.salary_range || '',
    status: props.offer.status,
});

const contractTypes = ['CDI', 'CDD', 'Stage', 'Alternance', 'Interim'];

const statusColor = (status) => {
    const map = { draft: 'warn', active: 'ok', archived: 'info' };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const map = { draft: 'Brouillon', active: 'Active', archived: 'Archivée' };
    return map[status] || status;
};

const updateOffer = () => {
    form.post(route('offers.update', props.offer.id), {
        onSuccess: () => { editing.value = false; },
    });
};

const archive = () => {
    if (confirm('Archiver cette offre ?')) {
        router.post(route('offers.archive', props.offer.id));
    }
};
</script>

<template>
    <Head :title="offer.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('offers.index')" class="text-sm text-brand-primary hover:underline mb-1 inline-block">&larr; Retour aux offres</Link>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">{{ offer.title }}</h2>
                </div>
                <div class="flex items-center gap-3">
                    <StatusBadge :label="statusLabel(offer.status)" :cls="statusColor(offer.status)" />
                    <SecondaryButton v-if="offer.status !== 'archived'" @click="archive">Archiver</SecondaryButton>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Détails de l'offre -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Détails de l'offre</h3>
                        <SecondaryButton v-if="!editing" @click="editing = true">Modifier</SecondaryButton>
                    </div>

                    <form v-if="editing" @submit.prevent="updateOffer" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <InputLabel value="Titre *" />
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
                                <TextInput v-model="form.salary_range" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Statut" />
                                <select v-model="form.status"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="draft">Brouillon</option>
                                    <option value="active">Active</option>
                                    <option value="archived">Archivée</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <InputLabel value="Description *" />
                            <textarea v-model="form.description" rows="5" required
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            <InputError :message="form.errors.description" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Prérequis" />
                            <textarea v-model="form.requirements" rows="4"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        </div>
                        <div class="flex gap-3">
                            <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                            <SecondaryButton @click="editing = false">Annuler</SecondaryButton>
                        </div>
                    </form>

                    <div v-else class="space-y-4">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 text-sm">
                            <div><span class="font-medium text-gray-500">Département :</span> {{ offer.department || '—' }}</div>
                            <div><span class="font-medium text-gray-500">Localisation :</span> {{ offer.location || '—' }}</div>
                            <div><span class="font-medium text-gray-500">Type de contrat :</span> {{ offer.contract_type || '—' }}</div>
                            <div><span class="font-medium text-gray-500">Salaire :</span> {{ offer.salary_range || '—' }}</div>
                            <div v-if="offer.published_at"><span class="font-medium text-gray-500">Publiée le :</span> {{ new Date(offer.published_at).toLocaleDateString('fr-FR') }}</div>
                            <div v-if="offer.created_by"><span class="font-medium text-gray-500">Créée par :</span> {{ offer.created_by.name }}</div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-1">Description</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ offer.description }}</p>
                        </div>
                        <div v-if="offer.requirements">
                            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-1">Prérequis</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ offer.requirements }}</p>
                        </div>
                    </div>
                </div>

                <!-- Candidats liés (via interview_reports) -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Candidats liés</h3>
                    <ul v-if="offer.interview_reports?.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li v-for="r in offer.interview_reports" :key="r.id" class="flex items-center justify-between py-3">
                            <div>
                                <Link :href="route('candidates.show', r.candidate.id)" class="text-sm font-medium text-gray-900 hover:text-brand-primary dark:text-gray-100">
                                    {{ r.candidate.first_name }} {{ r.candidate.last_name }}
                                </Link>
                                <div class="text-xs text-gray-500">Entretien le {{ new Date(r.interview_date).toLocaleDateString('fr-FR') }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span v-if="r.rating" class="text-sm text-yellow-600">{{ '★'.repeat(r.rating) }}{{ '☆'.repeat(5 - r.rating) }}</span>
                                <StatusBadge v-if="r.recommendation"
                                    :label="r.recommendation === 'hire' ? 'Embaucher' : r.recommendation === 'maybe' ? 'Peut-être' : 'Refuser'"
                                    :cls="r.recommendation === 'hire' ? 'ok' : r.recommendation === 'maybe' ? 'warn' : 'danger'" />
                            </div>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucun candidat lié à cette offre</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
