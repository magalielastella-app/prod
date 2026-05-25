<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    campaigns: { type: Array, default: () => [] },
    offers: { type: Array, default: () => [] },
});

const showModal = ref(false);
const editingCampaign = ref(null);

const form = useForm({
    title: '',
    description: '',
    job_offer_id: '',
    status: 'active',
});

const openCreate = () => {
    editingCampaign.value = null;
    form.reset();
    form.status = 'active';
    showModal.value = true;
};

const openEdit = (c) => {
    editingCampaign.value = c;
    form.title = c.title;
    form.description = c.description || '';
    form.job_offer_id = c.job_offer_id || '';
    form.status = c.status;
    showModal.value = true;
};

const submit = () => {
    const url = editingCampaign.value
        ? route('campaigns.update', editingCampaign.value.id)
        : route('campaigns.store');
    form.post(url, {
        onSuccess: () => { showModal.value = false; form.reset(); },
    });
};

const destroy = (c) => {
    if (!confirm(`Supprimer la campagne « ${c.title} » et dissocier ses candidats ?`)) return;
    useForm({}).post(route('campaigns.destroy', c.id));
};
</script>

<template>
    <Head title="Campagnes de recrutement" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Campagnes de recrutement
                </h2>
                <PrimaryButton @click="openCreate">+ Nouvelle campagne</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <div v-for="c in campaigns" :key="c.id"
                    class="overflow-hidden rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800 transition hover:shadow-md">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-bold text-gray-900">{{ c.title }}</h3>
                                <StatusBadge
                                    :label="c.status === 'active' ? 'Active' : 'Clôturée'"
                                    :cls="c.status === 'active' ? 'ok' : 'info'" />
                            </div>
                            <p v-if="c.description" class="mt-1 text-sm text-gray-600">{{ c.description }}</p>
                            <div class="mt-2 flex flex-wrap gap-4 text-xs text-gray-500">
                                <span v-if="c.job_offer">Offre : <strong>{{ c.job_offer.title }}</strong></span>
                                <span>{{ c.candidates_count }} candidat{{ c.candidates_count > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Link :href="route('candidates.index') + '?campaign=' + c.id"
                                class="text-sm text-brand-primary hover:underline">Voir candidats</Link>
                            <button class="text-sm text-brand-primary hover:underline" @click="openEdit(c)">Modifier</button>
                            <button class="text-sm text-red-600 hover:underline" @click="destroy(c)">Supprimer</button>
                        </div>
                    </div>
                </div>

                <div v-if="!campaigns.length" class="py-16 text-center text-sm italic text-gray-500">
                    Aucune campagne de recrutement. Créez-en une pour regrouper vos candidats.
                </div>
            </div>
        </div>

        <Modal :show="showModal" max-width="lg" @close="showModal = false">
            <form class="p-6" @submit.prevent="submit">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ editingCampaign ? 'Modifier la campagne' : 'Nouvelle campagne' }}
                </h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <InputLabel value="Titre de la campagne" />
                        <TextInput v-model="form.title" type="text" class="mt-1 block w-full" required
                            placeholder="ex : Recrutement assistante dentaire juin 2025" />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Offre d'emploi associée (optionnel)" />
                        <select v-model="form.job_offer_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                            <option value="">— aucune —</option>
                            <option v-for="o in offers" :key="o.id" :value="o.id">{{ o.title }}</option>
                        </select>
                    </div>
                    <div>
                        <InputLabel value="Description (optionnel)" />
                        <textarea v-model="form.description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary" />
                    </div>
                    <div v-if="editingCampaign">
                        <InputLabel value="Statut" />
                        <select v-model="form.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                            <option value="active">Active</option>
                            <option value="closed">Clôturée</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <SecondaryButton type="button" @click="showModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ editingCampaign ? 'Enregistrer' : 'Créer' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
