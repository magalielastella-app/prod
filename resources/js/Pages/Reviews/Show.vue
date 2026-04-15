<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    review: { type: Object, required: true },
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);
const isEmployee = computed(() => props.review.employee?.id === currentUser.value.id);
const isManagerOfReview = computed(
    () => props.review.manager?.id === currentUser.value.id || !!page.props.auth.isManager,
);

// Employee-side editable statuses
const employeeEditable = computed(
    () => isEmployee.value && ['scheduled', 'employee_draft'].includes(props.review.status),
);
// Manager-side editable statuses
const managerEditable = computed(
    () => isManagerOfReview.value
        && ['ready_for_manager', 'manager_draft', 'completed'].includes(props.review.status),
);

// ---------- Forms ----------
const selfForm = useForm({
    self_achievements: props.review.self_achievements ?? '',
    self_difficulties: props.review.self_difficulties ?? '',
    self_skills_developed: props.review.self_skills_developed ?? '',
    self_motivation: props.review.self_motivation ?? '',
    previous_objectives: props.review.previous_objectives?.length
        ? [...props.review.previous_objectives]
        : [{ title: '', result: '', achievement: '' }],
    employee_comments: props.review.employee_comments ?? '',
    submit: false,
});

const managerForm = useForm({
    new_objectives: props.review.new_objectives?.length
        ? [...props.review.new_objectives]
        : [{ title: '', description: '', deadline: '' }],
    training_needs: props.review.training_needs ?? '',
    career_development: props.review.career_development ?? '',
    manager_appreciation: props.review.manager_appreciation ?? '',
    manager_areas_for_improvement: props.review.manager_areas_for_improvement ?? '',
    overall_rating: props.review.overall_rating ?? null,
    manager_comments: props.review.manager_comments ?? '',
    finalize: false,
});

const addPreviousObjective = () => {
    selfForm.previous_objectives.push({ title: '', result: '', achievement: '' });
};
const removePreviousObjective = (i) => {
    selfForm.previous_objectives.splice(i, 1);
};
const addNewObjective = () => {
    managerForm.new_objectives.push({ title: '', description: '', deadline: '' });
};
const removeNewObjective = (i) => {
    managerForm.new_objectives.splice(i, 1);
};

const saveEmployee = (submit = false) => {
    selfForm.submit = submit;
    selfForm.put(route('reviews.employee.update', props.review.id), {
        preserveScroll: true,
        onFinish: () => (selfForm.submit = false),
    });
};

const saveManager = (finalize = false) => {
    managerForm.finalize = finalize;
    managerForm.put(route('reviews.manager.update', props.review.id), {
        preserveScroll: true,
        onFinish: () => (managerForm.finalize = false),
    });
};

const sign = () => {
    if (!confirm('Confirmer la signature de cet entretien ?')) return;
    router.post(route('reviews.sign', props.review.id), {}, { preserveScroll: true });
};

const statusColor = (status) => {
    if (status === 'signed') return 'ok';
    if (status === 'completed') return 'info';
    if (status === 'ready_for_manager') return 'warn';
    if (status === 'scheduled') return 'info';
    return 'warn';
};

const canEmployeeSign = computed(
    () => isEmployee.value
        && ['completed', 'signed'].includes(props.review.status)
        && !props.review.employee_signed_at,
);
const canManagerSign = computed(
    () => isManagerOfReview.value
        && ['completed', 'signed'].includes(props.review.status)
        && !props.review.manager_signed_at,
);
</script>

<template>
    <Head :title="`Entretien ${review.year} — ${review.employee?.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-start justify-between">
                <div>
                    <Link :href="route('reviews.index')" class="text-sm text-brand-primary hover:underline">
                        ← Liste des entretiens
                    </Link>
                    <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Entretien annuel {{ review.year }} — {{ review.employee?.name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Poste : {{ review.employee?.position || '—' }} ·
                        Service : {{ review.employee?.department || '—' }} ·
                        Manager : {{ review.manager?.name || '—' }}
                    </p>
                </div>
                <StatusBadge :label="review.status_label" :cls="statusColor(review.status)" />
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- ============ Bloc salarié ============ -->
                <section class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            1 · Auto-évaluation du salarié
                        </h3>
                        <span v-if="review.employee_signed_at" class="text-xs text-emerald-600">
                            Signé le {{ new Date(review.employee_signed_at).toLocaleString('fr-FR') }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <InputLabel value="Réalisations marquantes de l'année" />
                            <textarea v-model="selfForm.self_achievements" :disabled="!employeeEditable" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50" />
                        </div>
                        <div>
                            <InputLabel value="Difficultés rencontrées" />
                            <textarea v-model="selfForm.self_difficulties" :disabled="!employeeEditable" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50" />
                        </div>
                        <div>
                            <InputLabel value="Compétences développées" />
                            <textarea v-model="selfForm.self_skills_developed" :disabled="!employeeEditable" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50" />
                        </div>
                        <div>
                            <InputLabel value="Motivation / axes de satisfaction" />
                            <textarea v-model="selfForm.self_motivation" :disabled="!employeeEditable" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50" />
                        </div>

                        <!-- Bilan objectifs précédents -->
                        <div>
                            <div class="flex items-center justify-between">
                                <InputLabel value="Bilan des objectifs de l'année précédente" />
                                <SecondaryButton v-if="employeeEditable" type="button" @click="addPreviousObjective">
                                    + Ajouter
                                </SecondaryButton>
                            </div>
                            <div class="mt-2 space-y-3">
                                <div v-for="(obj, i) in selfForm.previous_objectives" :key="i"
                                    class="grid grid-cols-1 gap-2 rounded border border-gray-200 p-3 sm:grid-cols-12 dark:border-gray-700">
                                    <TextInput v-model="obj.title" :disabled="!employeeEditable"
                                        placeholder="Objectif" class="sm:col-span-5" />
                                    <TextInput v-model="obj.result" :disabled="!employeeEditable"
                                        placeholder="Résultat obtenu" class="sm:col-span-5" />
                                    <select v-model="obj.achievement" :disabled="!employeeEditable"
                                        class="sm:col-span-2 rounded-md border-gray-300 text-sm disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="">Niveau</option>
                                        <option value="Atteint">Atteint</option>
                                        <option value="Partiellement">Partiellement</option>
                                        <option value="Dépassé">Dépassé</option>
                                        <option value="Non atteint">Non atteint</option>
                                    </select>
                                    <button v-if="employeeEditable" type="button"
                                        class="sm:col-span-12 text-xs text-red-600 hover:underline justify-self-end"
                                        @click="removePreviousObjective(i)">
                                        Retirer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <InputLabel value="Commentaires du salarié" />
                            <textarea v-model="selfForm.employee_comments" :disabled="!employeeEditable" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50" />
                        </div>

                        <div v-if="employeeEditable" class="flex flex-wrap justify-end gap-2 pt-2">
                            <SecondaryButton :disabled="selfForm.processing" @click="saveEmployee(false)">
                                Enregistrer brouillon
                            </SecondaryButton>
                            <PrimaryButton :disabled="selfForm.processing" @click="saveEmployee(true)">
                                Envoyer au manager
                            </PrimaryButton>
                        </div>
                    </div>
                </section>

                <!-- ============ Bloc manager ============ -->
                <section class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            2 · Évaluation du manager
                        </h3>
                        <span v-if="review.manager_signed_at" class="text-xs text-emerald-600">
                            Signé le {{ new Date(review.manager_signed_at).toLocaleString('fr-FR') }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <InputLabel value="Appréciation générale du manager" />
                            <textarea v-model="managerForm.manager_appreciation" :disabled="!managerEditable" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50" />
                        </div>
                        <div>
                            <InputLabel value="Axes de progrès" />
                            <textarea v-model="managerForm.manager_areas_for_improvement" :disabled="!managerEditable" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50" />
                        </div>

                        <!-- Nouveaux objectifs -->
                        <div>
                            <div class="flex items-center justify-between">
                                <InputLabel value="Nouveaux objectifs" />
                                <SecondaryButton v-if="managerEditable" type="button" @click="addNewObjective">
                                    + Ajouter
                                </SecondaryButton>
                            </div>
                            <div class="mt-2 space-y-3">
                                <div v-for="(obj, i) in managerForm.new_objectives" :key="i"
                                    class="grid grid-cols-1 gap-2 rounded border border-gray-200 p-3 sm:grid-cols-12 dark:border-gray-700">
                                    <TextInput v-model="obj.title" :disabled="!managerEditable"
                                        placeholder="Objectif" class="sm:col-span-4" />
                                    <TextInput v-model="obj.description" :disabled="!managerEditable"
                                        placeholder="Description / indicateurs" class="sm:col-span-6" />
                                    <TextInput v-model="obj.deadline" :disabled="!managerEditable"
                                        placeholder="Échéance" class="sm:col-span-2" />
                                    <button v-if="managerEditable" type="button"
                                        class="sm:col-span-12 text-xs text-red-600 hover:underline justify-self-end"
                                        @click="removeNewObjective(i)">
                                        Retirer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel value="Besoins de formation" />
                                <textarea v-model="managerForm.training_needs" :disabled="!managerEditable" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50" />
                            </div>
                            <div>
                                <InputLabel value="Évolution / mobilité" />
                                <textarea v-model="managerForm.career_development" :disabled="!managerEditable" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel value="Évaluation globale (1 à 5)" />
                                <select v-model.number="managerForm.overall_rating" :disabled="!managerEditable"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50">
                                    <option :value="null">—</option>
                                    <option :value="1">1 — Insuffisant</option>
                                    <option :value="2">2 — À améliorer</option>
                                    <option :value="3">3 — Conforme</option>
                                    <option :value="4">4 — Très bien</option>
                                    <option :value="5">5 — Excellent</option>
                                </select>
                            </div>
                            <div>
                                <InputLabel value="Commentaires manager" />
                                <textarea v-model="managerForm.manager_comments" :disabled="!managerEditable" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50" />
                            </div>
                        </div>

                        <div v-if="managerEditable" class="flex flex-wrap justify-end gap-2 pt-2">
                            <SecondaryButton :disabled="managerForm.processing" @click="saveManager(false)">
                                Enregistrer brouillon
                            </SecondaryButton>
                            <PrimaryButton :disabled="managerForm.processing" @click="saveManager(true)">
                                Finaliser pour signature
                            </PrimaryButton>
                        </div>
                    </div>
                </section>

                <!-- ============ Bloc signatures ============ -->
                <section class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-gray-100">
                        3 · Signatures
                    </h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded border border-gray-200 p-4 dark:border-gray-700">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Salarié</div>
                            <div class="mt-1 text-xs text-gray-500">{{ review.employee?.name }}</div>
                            <div v-if="review.employee_signed_at" class="mt-2 text-sm text-emerald-600">
                                ✓ Signé le {{ new Date(review.employee_signed_at).toLocaleString('fr-FR') }}
                            </div>
                            <PrimaryButton v-else-if="canEmployeeSign" class="mt-3" @click="sign">
                                Signer électroniquement
                            </PrimaryButton>
                            <div v-else class="mt-2 text-xs italic text-gray-500">
                                En attente de la finalisation manager.
                            </div>
                        </div>
                        <div class="rounded border border-gray-200 p-4 dark:border-gray-700">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Manager</div>
                            <div class="mt-1 text-xs text-gray-500">{{ review.manager?.name || '—' }}</div>
                            <div v-if="review.manager_signed_at" class="mt-2 text-sm text-emerald-600">
                                ✓ Signé le {{ new Date(review.manager_signed_at).toLocaleString('fr-FR') }}
                            </div>
                            <PrimaryButton v-else-if="canManagerSign" class="mt-3" @click="sign">
                                Signer électroniquement
                            </PrimaryButton>
                            <div v-else class="mt-2 text-xs italic text-gray-500">
                                En attente de finalisation.
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
