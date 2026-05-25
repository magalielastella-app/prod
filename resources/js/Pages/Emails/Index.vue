<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    templates: { type: Array, default: () => [] },
    sentEmails: { type: Array, default: () => [] },
});

const tab = ref('templates'); // 'templates' | 'history'

// --- Modèle d'email ---
const showTemplateModal = ref(false);
const editingTemplate = ref(null);

const templateForm = useForm({
    name: '',
    subject: '',
    body: '',
    description: '',
});

const openCreateTemplate = () => {
    editingTemplate.value = null;
    templateForm.reset();
    showTemplateModal.value = true;
};

const openEditTemplate = (t) => {
    editingTemplate.value = t;
    templateForm.name = t.name;
    templateForm.subject = t.subject;
    templateForm.body = t.body;
    templateForm.description = t.description || '';
    showTemplateModal.value = true;
};

const submitTemplate = () => {
    const url = editingTemplate.value
        ? route('emails.templates.update', editingTemplate.value.id)
        : route('emails.templates.store');
    templateForm.post(url, {
        onSuccess: () => { showTemplateModal.value = false; templateForm.reset(); },
    });
};

const destroyTemplate = (t) => {
    if (!confirm(`Supprimer le modèle « ${t.name} » ?`)) return;
    useForm({}).post(route('emails.templates.destroy', t.id));
};
</script>

<template>
    <Head title="Emails" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Emails
                </h2>
                <PrimaryButton @click="openCreateTemplate">+ Nouveau modèle</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Onglets -->
                <div class="flex gap-2">
                    <button @click="tab = 'templates'"
                        :class="['rounded-lg px-4 py-2 text-sm font-semibold transition',
                            tab === 'templates' ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50']">
                        Modèles d'email ({{ templates.length }})
                    </button>
                    <button @click="tab = 'history'"
                        :class="['rounded-lg px-4 py-2 text-sm font-semibold transition',
                            tab === 'history' ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50']">
                        Historique d'envoi ({{ sentEmails.length }})
                    </button>
                </div>

                <!-- Modèles -->
                <div v-if="tab === 'templates'" class="space-y-4">
                    <div v-for="t in templates" :key="t.id"
                        class="rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ t.name }}</h3>
                                <p class="mt-1 text-sm text-gray-500">Objet : {{ t.subject }}</p>
                                <p v-if="t.description" class="mt-1 text-xs text-gray-400 italic">{{ t.description }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button class="text-sm text-brand-primary hover:underline" @click="openEditTemplate(t)">Modifier</button>
                                <button class="text-sm text-red-600 hover:underline" @click="destroyTemplate(t)">Supprimer</button>
                            </div>
                        </div>
                        <div class="mt-3 rounded bg-gray-50 p-3 text-sm text-gray-700 whitespace-pre-wrap dark:bg-gray-900 dark:text-gray-300">
                            {{ t.body }}
                        </div>
                    </div>
                    <div v-if="!templates.length" class="py-10 text-center text-sm italic text-gray-500">
                        Aucun modèle d'email. Créez-en un pour commencer.
                    </div>
                </div>

                <!-- Historique -->
                <div v-if="tab === 'history'" class="overflow-hidden rounded-xl bg-white shadow-soft dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Candidat</th>
                                <th class="px-4 py-3">Objet</th>
                                <th class="px-4 py-3">Modèle</th>
                                <th class="px-4 py-3">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="e in sentEmails" :key="e.id" class="text-sm text-gray-700 dark:text-gray-200">
                                <td class="px-4 py-3">{{ e.sent_at }}</td>
                                <td class="px-4 py-3 font-medium">{{ e.candidate_name }}</td>
                                <td class="px-4 py-3">{{ e.subject }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ e.template_name || '—' }}</td>
                                <td class="px-4 py-3">
                                    <StatusBadge :label="e.status === 'sent' ? 'Envoyé' : 'Échec'" :cls="e.status === 'sent' ? 'ok' : 'danger'" />
                                </td>
                            </tr>
                            <tr v-if="!sentEmails.length">
                                <td colspan="5" class="px-4 py-10 text-center text-sm italic text-gray-500">
                                    Aucun email envoyé pour le moment.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal modèle -->
        <Modal :show="showTemplateModal" max-width="2xl" @close="showTemplateModal = false">
            <form class="p-6" @submit.prevent="submitTemplate">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ editingTemplate ? 'Modifier le modèle' : 'Nouveau modèle d\'email' }}
                </h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <InputLabel value="Nom du modèle (usage interne)" />
                        <TextInput v-model="templateForm.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="templateForm.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Objet de l'email" />
                        <TextInput v-model="templateForm.subject" type="text" class="mt-1 block w-full" required />
                        <InputError :message="templateForm.errors.subject" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Corps du message" />
                        <p class="text-xs text-gray-500">Variables disponibles : {prenom}, {nom}, {poste}, {date}, {heure}, {lieu}</p>
                        <textarea v-model="templateForm.body" rows="10"
                            class="mt-1 block w-full rounded border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            required />
                        <InputError :message="templateForm.errors.body" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Description (optionnel)" />
                        <TextInput v-model="templateForm.description" type="text" class="mt-1 block w-full" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <SecondaryButton type="button" @click="showTemplateModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="templateForm.processing">
                        {{ editingTemplate ? 'Enregistrer' : 'Créer' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
