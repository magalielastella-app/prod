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
import { ref, computed } from 'vue';

const props = defineProps({
    events: { type: Object, required: true },
    candidates: { type: Array, default: () => [] },
    offers: { type: Array, default: () => [] },
    interviewers: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const showModal = ref(false);

const form = useForm({
    candidate_id: '',
    job_offer_id: '',
    interviewer_id: '',
    title: '',
    scheduled_at: '',
    duration_minutes: 60,
    location: '',
    notes: '',
});

const statusColor = (status) => {
    const map = { scheduled: 'info', completed: 'ok', cancelled: 'danger' };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const map = { scheduled: 'Planifié', completed: 'Terminé', cancelled: 'Annulé' };
    return map[status] || status;
};

// Group events by date
const groupedEvents = computed(() => {
    const groups = {};
    for (const event of props.events.data) {
        const date = new Date(event.scheduled_at).toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        if (!groups[date]) groups[date] = [];
        groups[date].push(event);
    }
    return groups;
});

const submit = () => {
    form.post(route('agenda.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            form.duration_minutes = 60;
        },
    });
};

const cancel = (id) => {
    if (confirm('Annuler cet entretien ?')) {
        router.post(route('agenda.destroy', id));
    }
};
</script>

<template>
    <Head title="Agenda" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">Agenda des entretiens</h2>
                <PrimaryButton @click="showModal = true">+ Planifier un entretien</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Liste groupée par jour -->
                <div v-if="Object.keys(groupedEvents).length" class="space-y-6">
                    <div v-for="(dayEvents, date) in groupedEvents" :key="date">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 mb-3 capitalize">{{ date }}</h3>
                        <div class="space-y-3">
                            <div v-for="e in dayEvents" :key="e.id"
                                class="flex items-center justify-between rounded-xl bg-white p-4 shadow-soft dark:bg-gray-800 transition hover:shadow-md">
                                <div class="flex items-center gap-4">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-brand-primary">
                                            {{ new Date(e.scheduled_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ e.duration_minutes }} min</div>
                                    </div>
                                    <div class="border-l border-gray-200 pl-4 dark:border-gray-700">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ e.title }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            <span v-if="e.candidate">{{ e.candidate.first_name }} {{ e.candidate.last_name }}</span>
                                            <span v-if="e.job_offer"> - {{ e.job_offer.title }}</span>
                                        </div>
                                        <div v-if="e.location" class="text-xs text-gray-400 mt-0.5">{{ e.location }}</div>
                                        <div v-if="e.interviewer" class="text-xs text-gray-400">Interviewer : {{ e.interviewer.name }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <StatusBadge :label="statusLabel(e.status)" :cls="statusColor(e.status)" />
                                    <button v-if="e.status === 'scheduled'" @click="cancel(e.id)"
                                        class="text-xs text-pink-600 hover:underline">Annuler</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="rounded-xl bg-white p-8 shadow-soft text-center dark:bg-gray-800">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-brand-peach text-orange-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="mt-3 text-sm italic text-gray-500">Aucun entretien planifié</p>
                </div>

                <!-- Pagination -->
                <div v-if="events.links && events.links.length > 3" class="mt-6 flex justify-center gap-1">
                    <template v-for="link in events.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            class="rounded px-3 py-1 text-sm transition"
                            :class="link.active ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-brand-tertiary'"
                            v-html="link.label" />
                        <span v-else class="rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal planification -->
        <Modal :show="showModal" @close="showModal = false" max-width="2xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Planifier un entretien</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <InputLabel value="Titre *" />
                            <TextInput v-model="form.title" class="mt-1 w-full" required placeholder="Ex: Entretien technique" />
                            <InputError :message="form.errors.title" class="mt-1" />
                        </div>
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
                            <InputLabel value="Date et heure *" />
                            <TextInput v-model="form.scheduled_at" type="datetime-local" class="mt-1 w-full" required />
                            <InputError :message="form.errors.scheduled_at" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Durée (minutes)" />
                            <TextInput v-model.number="form.duration_minutes" type="number" min="15" max="480" class="mt-1 w-full" />
                        </div>
                        <div>
                            <InputLabel value="Lieu" />
                            <TextInput v-model="form.location" class="mt-1 w-full" placeholder="Salle, visio..." />
                        </div>
                        <div>
                            <InputLabel value="Interviewer" />
                            <select v-model="form.interviewer_id"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Sélectionner</option>
                                <option v-for="u in interviewers" :key="u.id" :value="u.id">{{ u.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <InputLabel value="Notes" />
                        <textarea v-model="form.notes" rows="2"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Planifier</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
