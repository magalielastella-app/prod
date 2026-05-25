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
    reports: { type: Object, required: true },
    candidates: { type: Array, default: () => [] },
    offers: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const showModal = ref(false);
const candidateFilter = ref(props.filters.candidate_id || '');

const form = useForm({
    candidate_id: '',
    job_offer_id: '',
    interview_date: '',
    rating: '',
    strengths: '',
    weaknesses: '',
    notes: '',
    recommendation: '',
});

watch(candidateFilter, () => {
    router.get(route('reports.index'), {
        candidate_id: candidateFilter.value || undefined,
    }, { preserveState: true, replace: true });
});

const recommendationLabel = (r) => {
    const map = { hire: 'Embaucher', maybe: 'Peut-être', reject: 'Refuser' };
    return map[r] || r;
};

const recommendationColor = (r) => {
    const map = { hire: 'ok', maybe: 'warn', reject: 'danger' };
    return map[r] || 'info';
};

const submit = () => {
    form.post(route('reports.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const destroy = (id) => {
    if (confirm('Supprimer ce compte-rendu ?')) {
        router.post(route('reports.destroy', id));
    }
};
</script>

<template>
    <Head title="Comptes-rendus" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">Comptes-rendus d'entretien</h2>
                <PrimaryButton @click="showModal = true">+ Nouveau compte-rendu</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filtre -->
                <div class="mb-6">
                    <select v-model="candidateFilter"
                        class="rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Tous les candidats</option>
                        <option v-for="c in candidates" :key="c.id" :value="c.id">{{ c.first_name }} {{ c.last_name }}</option>
                    </select>
                </div>

                <!-- Tableau -->
                <div class="overflow-hidden rounded-xl bg-white shadow-soft dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-brand-tertiary/50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Candidat</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Offre</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Note</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Recommandation</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="r in reports.data" :key="r.id" class="hover:bg-brand-tertiary/30 transition">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ new Date(r.interview_date).toLocaleDateString('fr-FR') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    <Link v-if="r.candidate" :href="route('candidates.show', r.candidate.id)" class="hover:text-brand-primary">
                                        {{ r.candidate.first_name }} {{ r.candidate.last_name }}
                                    </Link>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ r.job_offer?.title || '—' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-yellow-600">
                                    <span v-if="r.rating">{{ '★'.repeat(r.rating) }}{{ '☆'.repeat(5 - r.rating) }}</span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <StatusBadge v-if="r.recommendation" :label="recommendationLabel(r.recommendation)" :cls="recommendationColor(r.recommendation)" />
                                    <span v-else class="text-sm text-gray-400">—</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <button @click="destroy(r.id)" class="text-pink-600 hover:underline">Supprimer</button>
                                </td>
                            </tr>
                            <tr v-if="!reports.data.length">
                                <td colspan="6" class="px-6 py-8 text-center text-sm italic text-gray-500">Aucun compte-rendu</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="reports.links && reports.links.length > 3" class="mt-4 flex justify-center gap-1">
                    <template v-for="link in reports.links" :key="link.label">
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Nouveau compte-rendu</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel value="Candidat *" />
                            <select v-model="form.candidate_id" required
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Sélectionner</option>
                                <option v-for="c in candidates" :key="c.id" :value="c.id">{{ c.first_name }} {{ c.last_name }}</option>
                            </select>
                            <InputError :message="form.errors.candidate_id" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Offre d'emploi" />
                            <select v-model="form.job_offer_id"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Aucune</option>
                                <option v-for="o in offers" :key="o.id" :value="o.id">{{ o.title }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Date de l'entretien *" />
                            <TextInput v-model="form.interview_date" type="date" class="mt-1 w-full" required />
                            <InputError :message="form.errors.interview_date" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Note (1-5)" />
                            <select v-model="form.rating"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Sans note</option>
                                <option v-for="n in 5" :key="n" :value="n">{{ '★'.repeat(n) }} ({{ n }}/5)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <InputLabel value="Points forts" />
                        <textarea v-model="form.strengths" rows="2"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <InputLabel value="Points faibles" />
                        <textarea v-model="form.weaknesses" rows="2"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <InputLabel value="Notes" />
                        <textarea v-model="form.notes" rows="3"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <InputLabel value="Recommandation" />
                        <select v-model="form.recommendation"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">Sans recommandation</option>
                            <option value="hire">Embaucher</option>
                            <option value="maybe">Peut-être</option>
                            <option value="reject">Refuser</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Créer</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
