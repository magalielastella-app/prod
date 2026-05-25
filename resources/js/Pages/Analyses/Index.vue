<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    analyses: { type: Object, required: true },
    candidates: { type: Array, default: () => [] },
    apiKeyConfigured: { type: Boolean, default: false },
});

const showModal = ref(false);

const form = useForm({
    candidate_id: '',
    cv_document_id: '',
    prompt: '',
});

const submit = () => {
    form.post(route('analyses.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const destroy = (id) => {
    if (confirm('Supprimer cette analyse ?')) {
        router.post(route('analyses.destroy', id));
    }
};
</script>

<template>
    <Head title="Analyses IA" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">Analyses IA</h2>
                <PrimaryButton @click="showModal = true">+ Nouvelle analyse</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Avertissement API key -->
                <div v-if="!apiKeyConfigured" class="rounded-xl bg-brand-peach p-4 shadow-soft">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-orange-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <p class="text-sm font-medium text-orange-800">
                            Configurez ANTHROPIC_API_KEY pour activer l'analyse IA
                        </p>
                    </div>
                </div>

                <!-- Liste des analyses -->
                <div class="space-y-4">
                    <div v-for="a in analyses.data" :key="a.id"
                        class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                    {{ a.candidate?.first_name }} {{ a.candidate?.last_name }}
                                </h4>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ new Date(a.created_at).toLocaleDateString('fr-FR') }}
                                    <span v-if="a.model_used"> - Modèle : {{ a.model_used }}</span>
                                    <span v-if="a.cv_document"> - CV : {{ a.cv_document.original_name }}</span>
                                </div>
                            </div>
                            <button @click="destroy(a.id)" class="text-sm text-pink-600 hover:underline">Supprimer</button>
                        </div>
                        <div class="rounded-lg bg-brand-tertiary/50 p-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ a.analysis }}</p>
                        </div>
                        <div v-if="a.prompt_used" class="mt-3">
                            <details class="text-xs text-gray-500">
                                <summary class="cursor-pointer hover:text-brand-primary">Voir le prompt utilisé</summary>
                                <p class="mt-2 rounded bg-gray-50 p-2 dark:bg-gray-700">{{ a.prompt_used }}</p>
                            </details>
                        </div>
                    </div>

                    <div v-if="!analyses.data.length" class="rounded-xl bg-white p-8 shadow-soft text-center dark:bg-gray-800">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-brand-sky text-sky-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm italic text-gray-500">Aucune analyse sauvegardée</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="analyses.links && analyses.links.length > 3" class="flex justify-center gap-1">
                    <template v-for="link in analyses.links" :key="link.label">
                        <a v-if="link.url" :href="link.url"
                            class="rounded px-3 py-1 text-sm transition"
                            :class="link.active ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-brand-tertiary'"
                            v-html="link.label" />
                        <span v-else class="rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal nouvelle analyse -->
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Lancer une analyse IA</h3>

                <div v-if="!apiKeyConfigured" class="mb-4 rounded-lg bg-brand-peach p-3">
                    <p class="text-sm text-orange-800">Configurez ANTHROPIC_API_KEY pour activer l'analyse IA</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel value="Candidat *" />
                        <select v-model="form.candidate_id" required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">Sélectionner un candidat</option>
                            <option v-for="c in candidates" :key="c.id" :value="c.id">
                                {{ c.first_name }} {{ c.last_name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.candidate_id" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Prompt personnalisé (optionnel)" />
                        <textarea v-model="form.prompt" rows="4" placeholder="Laissez vide pour utiliser le prompt par défaut..."
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        <InputError :message="form.errors.prompt" class="mt-1" />
                    </div>

                    <InputError :message="form.errors.api" class="mt-1" />

                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing || !apiKeyConfigured">
                            Analyser avec Claude
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
