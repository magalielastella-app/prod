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
    candidate: { type: Object, required: true },
});

const editing = ref(false);

const form = useForm({
    first_name: props.candidate.first_name,
    last_name: props.candidate.last_name,
    email: props.candidate.email || '',
    phone: props.candidate.phone || '',
    city: props.candidate.city || '',
    status: props.candidate.status,
    source: props.candidate.source || '',
    notes: props.candidate.notes || '',
});

const cvForm = useForm({ cv_file: null });
const fileInput = ref(null);

const statuses = [
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
    const s = statuses.find(st => st.value === status);
    return s ? s.label : status;
};

const updateCandidate = () => {
    form.post(route('candidates.update', props.candidate.id), {
        onSuccess: () => { editing.value = false; },
    });
};

const changeStatus = (status) => {
    router.post(route('candidates.update', props.candidate.id), {
        ...form.data(),
        status,
    });
};

const uploadCv = () => {
    if (!fileInput.value?.files[0]) return;
    cvForm.cv_file = fileInput.value.files[0];
    cvForm.post(route('cv.store', props.candidate.id), {
        onSuccess: () => {
            cvForm.reset();
            if (fileInput.value) fileInput.value.value = '';
        },
    });
};

const deleteCv = (cvId) => {
    if (confirm('Supprimer ce CV ?')) {
        router.post(route('cv.destroy', cvId));
    }
};

const recommendationLabel = (r) => {
    const map = { hire: 'Embaucher', maybe: 'Peut-être', reject: 'Refuser' };
    return map[r] || r;
};

const recommendationColor = (r) => {
    const map = { hire: 'ok', maybe: 'warn', reject: 'danger' };
    return map[r] || 'info';
};
</script>

<template>
    <Head :title="`${candidate.first_name} ${candidate.last_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('candidates.index')" class="text-sm text-brand-primary hover:underline mb-1 inline-block">&larr; Retour à la CVthèque</Link>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">
                        {{ candidate.first_name }} {{ candidate.last_name }}
                    </h2>
                </div>
                <StatusBadge :label="statusLabel(candidate.status)" :cls="statusColor(candidate.status)" />
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Info section -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Informations</h3>
                        <SecondaryButton v-if="!editing" @click="editing = true">Modifier</SecondaryButton>
                    </div>

                    <form v-if="editing" @submit.prevent="updateCandidate" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel value="Prénom *" />
                                <TextInput v-model="form.first_name" class="mt-1 w-full" required />
                                <InputError :message="form.errors.first_name" class="mt-1" />
                            </div>
                            <div>
                                <InputLabel value="Nom *" />
                                <TextInput v-model="form.last_name" class="mt-1 w-full" required />
                                <InputError :message="form.errors.last_name" class="mt-1" />
                            </div>
                            <div>
                                <InputLabel value="Email" />
                                <TextInput v-model="form.email" type="email" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Téléphone" />
                                <TextInput v-model="form.phone" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Ville" />
                                <TextInput v-model="form.city" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Source" />
                                <TextInput v-model="form.source" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Statut" />
                                <select v-model="form.status"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <InputLabel value="Notes" />
                            <textarea v-model="form.notes" rows="3"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        </div>
                        <div class="flex gap-3">
                            <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                            <SecondaryButton @click="editing = false">Annuler</SecondaryButton>
                        </div>
                    </form>

                    <div v-else class="grid grid-cols-1 gap-3 sm:grid-cols-2 text-sm">
                        <div><span class="font-medium text-gray-500">Email :</span> {{ candidate.email || '—' }}</div>
                        <div><span class="font-medium text-gray-500">Téléphone :</span> {{ candidate.phone || '—' }}</div>
                        <div><span class="font-medium text-gray-500">Ville :</span> {{ candidate.city || '—' }}</div>
                        <div><span class="font-medium text-gray-500">Source :</span> {{ candidate.source || '—' }}</div>
                        <div class="sm:col-span-2"><span class="font-medium text-gray-500">Notes :</span> {{ candidate.notes || '—' }}</div>
                    </div>
                </div>

                <!-- Changement de statut rapide -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Changer le statut</h3>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="s in statuses" :key="s.value"
                            @click="changeStatus(s.value)"
                            :class="['rounded-full px-4 py-2 text-sm font-medium transition',
                                candidate.status === s.value
                                    ? 'bg-brand-primary text-white'
                                    : 'bg-brand-tertiary text-gray-700 hover:bg-brand-primary/20']">
                            {{ s.label }}
                        </button>
                    </div>
                </div>

                <!-- CV Section -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Documents CV</h3>

                    <div class="mb-4 flex items-center gap-3">
                        <input ref="fileInput" type="file" accept=".pdf,.doc,.docx" @change="uploadCv"
                            class="text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-brand-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-primary-dark" />
                        <InputError :message="cvForm.errors.cv_file" />
                    </div>

                    <ul v-if="candidate.cv_documents?.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li v-for="cv in candidate.cv_documents" :key="cv.id" class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ cv.original_name }}</div>
                                    <div class="text-xs text-gray-500">{{ (cv.file_size / 1024).toFixed(0) }} Ko - {{ new Date(cv.created_at).toLocaleDateString('fr-FR') }}</div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a :href="route('cv.download', cv.id)" class="text-sm text-brand-primary hover:underline">Télécharger</a>
                                <button @click="deleteCv(cv.id)" class="text-sm text-pink-600 hover:underline">Supprimer</button>
                            </div>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucun CV uploadé</p>
                </div>

                <!-- Analyses Section -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Analyses IA</h3>
                        <Link :href="route('analyses.index')" class="text-sm text-brand-primary hover:underline">Nouvelle analyse</Link>
                    </div>
                    <ul v-if="candidate.analyses?.length" class="space-y-3">
                        <li v-for="a in candidate.analyses" :key="a.id" class="rounded-lg border border-gray-100 p-4 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-gray-500">{{ new Date(a.created_at).toLocaleDateString('fr-FR') }} - {{ a.model_used || 'Claude' }}</span>
                                <span v-if="a.cv_document" class="text-xs text-brand-primary">CV: {{ a.cv_document.original_name }}</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap line-clamp-4">{{ a.analysis }}</p>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucune analyse sauvegardée</p>
                </div>

                <!-- Interview Reports -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Comptes-rendus d'entretien</h3>
                    <ul v-if="candidate.interview_reports?.length" class="space-y-3">
                        <li v-for="r in candidate.interview_reports" :key="r.id" class="rounded-lg border border-gray-100 p-4 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ new Date(r.interview_date).toLocaleDateString('fr-FR') }}</span>
                                <div class="flex items-center gap-2">
                                    <span v-if="r.rating" class="text-sm text-yellow-600">{{ '★'.repeat(r.rating) }}{{ '☆'.repeat(5 - r.rating) }}</span>
                                    <StatusBadge v-if="r.recommendation" :label="recommendationLabel(r.recommendation)" :cls="recommendationColor(r.recommendation)" />
                                </div>
                            </div>
                            <div v-if="r.job_offer" class="text-xs text-gray-500 mb-1">Offre : {{ r.job_offer.title }}</div>
                            <div v-if="r.interviewer" class="text-xs text-gray-500 mb-2">Interviewer : {{ r.interviewer.name }}</div>
                            <p v-if="r.notes" class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ r.notes }}</p>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucun compte-rendu</p>
                </div>

                <!-- Scheduled Events -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Entretiens planifiés</h3>
                    <ul v-if="candidate.interview_events?.length" class="space-y-3">
                        <li v-for="e in candidate.interview_events" :key="e.id" class="flex items-center justify-between rounded-lg border border-gray-100 p-4 dark:border-gray-700">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ e.title }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ new Date(e.scheduled_at).toLocaleString('fr-FR') }}
                                    <span v-if="e.location"> - {{ e.location }}</span>
                                </div>
                                <div v-if="e.job_offer" class="text-xs text-gray-500">Offre : {{ e.job_offer.title }}</div>
                            </div>
                            <StatusBadge
                                :label="e.status === 'scheduled' ? 'Planifié' : e.status === 'completed' ? 'Terminé' : 'Annulé'"
                                :cls="e.status === 'scheduled' ? 'info' : e.status === 'completed' ? 'ok' : 'danger'" />
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucun entretien planifié</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
