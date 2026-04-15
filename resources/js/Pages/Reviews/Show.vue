<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    review: { type: Object, required: true },
    template: { type: Object, required: true },
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);

const isEmployee = computed(() => props.review.employee?.id === currentUser.value.id);
const isManagerOfReview = computed(
    () => props.review.manager?.id === currentUser.value.id
        || currentUser.value.role === 'admin',
);

const employeeEditable = computed(
    () => isEmployee.value
        && ['scheduled', 'employee_draft'].includes(props.review.status),
);
const managerEditable = computed(
    () => isManagerOfReview.value
        && ['ready_for_manager', 'manager_draft', 'completed'].includes(props.review.status),
);

// Deux formulaires : un pour la partie salarié, un pour la partie manager.
const employeeForm = useForm({
    header: { ...(props.review.header || {}) },
    answers: { ...(props.review.employee_answers || {}) },
    submit: false,
});
const managerForm = useForm({
    header: { ...(props.review.header || {}) },
    answers: { ...(props.review.manager_answers || {}) },
    finalize: false,
});

// Renvoie le bon "sac" de réponses selon le propriétaire et le mode (édition ou lecture)
const readAnswers = (owner) => {
    if (owner === 'manager') {
        return managerEditable.value ? managerForm.answers : (props.review.manager_answers || {});
    }
    return employeeEditable.value ? employeeForm.answers : (props.review.employee_answers || {});
};

const readHeader = (owner) => {
    if (owner === 'manager') {
        return managerEditable.value ? managerForm.header : (props.review.header || {});
    }
    return employeeEditable.value ? employeeForm.header : (props.review.header || {});
};

const canEditField = (owner) => {
    if (owner === 'employee') return employeeEditable.value;
    if (owner === 'manager') return managerEditable.value;
    return false;
};

// --- Actions ligne (tableaux dynamiques) ---
const addRow = (owner, key, rowTemplate) => {
    const bag = readAnswers(owner);
    if (!Array.isArray(bag[key])) bag[key] = [];
    bag[key].push({ ...rowTemplate });
};
const removeRow = (owner, key, index) => {
    const bag = readAnswers(owner);
    if (Array.isArray(bag[key])) bag[key].splice(index, 1);
};

// --- Grille de compétences : accès cellules ---
const getGridCell = (owner, key, rowIndex, cellKey = null) => {
    const bag = readAnswers(owner);
    const arr = Array.isArray(bag[key]) ? bag[key] : [];
    const cell = arr[rowIndex];
    if (cell === undefined || cell === null) return cellKey ? '' : '';
    if (cellKey) return typeof cell === 'object' ? (cell[cellKey] ?? '') : '';
    return cell;
};
const setGridCell = (owner, key, rowIndex, value, cellKey = null) => {
    const bag = readAnswers(owner);
    if (!Array.isArray(bag[key])) bag[key] = [];
    while (bag[key].length <= rowIndex) bag[key].push(cellKey ? {} : '');
    if (cellKey) {
        if (typeof bag[key][rowIndex] !== 'object' || bag[key][rowIndex] === null) {
            bag[key][rowIndex] = {};
        }
        bag[key][rowIndex][cellKey] = value;
    } else {
        bag[key][rowIndex] = value;
    }
};

// --- Sauvegarde ---
const saveEmployee = (submit = false) => {
    employeeForm.submit = submit;
    employeeForm.put(route('reviews.employee.update', props.review.id), {
        preserveScroll: true,
        onFinish: () => { employeeForm.submit = false; },
    });
};
const saveManager = (finalize = false) => {
    managerForm.finalize = finalize;
    managerForm.put(route('reviews.manager.update', props.review.id), {
        preserveScroll: true,
        onFinish: () => { managerForm.finalize = false; },
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

// classe réutilisée pour les inputs
const inputCls = 'block w-full rounded border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50';
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
                </div>
                <div class="flex items-center gap-3">
                    <a :href="route('reviews.print', review.id)" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-brand-primary/40 bg-white px-3 py-1.5 text-xs font-semibold text-brand-primary shadow-sm transition hover:bg-brand-tertiary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Télécharger PDF
                    </a>
                    <StatusBadge :label="review.status_label" :cls="statusColor(review.status)" />
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Entête -->
                <section class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-brand-primary">
                            {{ template.label }} — Entretien annuel {{ review.year }}
                        </h3>
                        <p class="text-xs text-gray-500">Trame : {{ template.key }}</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div><span class="font-medium">Nom :</span> {{ review.employee?.name }}</div>
                        <div><span class="font-medium">Poste :</span> {{ review.employee?.position || '—' }}</div>
                        <div><span class="font-medium">Date d'embauche :</span> {{ review.employee?.hired_on || '—' }}</div>
                        <div><span class="font-medium">Date d'entretien :</span> {{ review.scheduled_for || '—' }}</div>
                    </div>

                    <div v-if="template.header?.length" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div v-for="h in template.header" :key="h.key">
                            <InputLabel :value="h.label" />
                            <input type="text"
                                :value="readHeader(h.owner)[h.key]"
                                @input="(e) => { readHeader(h.owner)[h.key] = e.target.value; }"
                                :disabled="!canEditField(h.owner)"
                                :class="['mt-1', inputCls]" />
                            <p v-if="canEditField(h.owner)" class="mt-1 text-[10px] italic text-gray-500">
                                Champ {{ h.owner === 'manager' ? 'manager' : 'salarié' }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Sections de la trame -->
                <section v-for="(sec, si) in template.sections" :key="si"
                    class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 border-b border-brand-primary/40 pb-2 text-base font-bold text-brand-primary">
                        {{ sec.title }}
                    </h3>
                    <div class="space-y-5">
                        <div v-for="field in sec.fields" :key="field.key">

                            <!-- Échelle 1 à 10 -->
                            <template v-if="field.type === 'scale_10'">
                                <InputLabel :value="field.question" />
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <button v-for="n in 10" :key="n" type="button"
                                        :disabled="!canEditField(field.owner)"
                                        @click="readAnswers(field.owner)[field.key] = n"
                                        :class="[
                                            'h-10 w-10 rounded border text-sm font-semibold transition',
                                            readAnswers(field.owner)[field.key] === n
                                                ? 'border-amber-500 bg-amber-300 text-gray-900'
                                                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
                                            !canEditField(field.owner) ? 'cursor-not-allowed opacity-80' : 'cursor-pointer',
                                        ]">
                                        {{ n }}
                                    </button>
                                </div>
                            </template>

                            <!-- Zone de texte -->
                            <template v-else-if="field.type === 'textarea'">
                                <InputLabel :value="field.question" />
                                <textarea rows="3"
                                    :value="readAnswers(field.owner)[field.key]"
                                    @input="(e) => { readAnswers(field.owner)[field.key] = e.target.value; }"
                                    :disabled="!canEditField(field.owner)"
                                    :class="['mt-1', inputCls]" />
                            </template>

                            <!-- Champ texte court -->
                            <template v-else-if="field.type === 'text'">
                                <InputLabel :value="field.question" />
                                <input type="text"
                                    :value="readAnswers(field.owner)[field.key]"
                                    @input="(e) => { readAnswers(field.owner)[field.key] = e.target.value; }"
                                    :disabled="!canEditField(field.owner)"
                                    :class="['mt-1', inputCls]" />
                            </template>

                            <!-- Choix unique (pastilles) -->
                            <template v-else-if="field.type === 'choice'">
                                <InputLabel :value="field.question" />
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <button v-for="opt in field.options" :key="opt" type="button"
                                        :disabled="!canEditField(field.owner)"
                                        @click="readAnswers(field.owner)[field.key] = opt"
                                        :class="[
                                            'rounded-full border px-3 py-1 text-sm transition',
                                            readAnswers(field.owner)[field.key] === opt
                                                ? 'border-brand-primary bg-brand-primary text-white'
                                                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
                                            !canEditField(field.owner) ? 'cursor-not-allowed opacity-80' : 'cursor-pointer',
                                        ]">
                                        {{ opt }}
                                    </button>
                                </div>
                            </template>

                            <!-- Bilan des objectifs -->
                            <template v-else-if="field.type === 'objectives_review'">
                                <div class="flex items-center justify-between">
                                    <InputLabel :value="field.question" />
                                    <button v-if="canEditField('manager')" type="button"
                                        class="text-xs text-brand-primary hover:underline"
                                        @click="addRow('manager', field.key, { objectif: '', evaluation: '' })">
                                        + Ajouter un objectif
                                    </button>
                                </div>
                                <p v-if="field.hint" class="text-xs italic text-gray-500">{{ field.hint }}</p>
                                <table class="mt-2 w-full border-collapse text-sm">
                                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="p-2">Objectif fixé</th>
                                            <th class="w-52 p-2">Évaluation manager</th>
                                            <th v-if="canEditField('manager')" class="w-8 p-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, i) in (readAnswers('manager')[field.key] || [])" :key="i"
                                            class="border-t border-gray-100">
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('manager')[field.key][i].objectif"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <select v-model="readAnswers('manager')[field.key][i].evaluation"
                                                    :disabled="!canEditField('manager')" :class="inputCls">
                                                    <option value="">—</option>
                                                    <option v-for="opt in field.evaluation_options" :key="opt" :value="opt">
                                                        {{ opt }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td v-if="canEditField('manager')" class="p-1 text-center">
                                                <button type="button" class="text-xs text-red-600 hover:underline"
                                                    @click="removeRow('manager', field.key, i)">✕</button>
                                            </td>
                                        </tr>
                                        <tr v-if="!(readAnswers('manager')[field.key] || []).length">
                                            <td colspan="3" class="p-3 text-center text-xs italic text-gray-500">
                                                Aucun objectif enregistré
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </template>

                            <!-- Activités / réussites / difficultés -->
                            <template v-else-if="field.type === 'activities_table'">
                                <div class="flex items-center justify-between">
                                    <InputLabel :value="field.question" />
                                    <button v-if="canEditField('employee')" type="button"
                                        class="text-xs text-brand-primary hover:underline"
                                        @click="addRow('employee', field.key, { realisations: '', reussites: '', difficultes: '' })">
                                        + Ajouter
                                    </button>
                                </div>
                                <table class="mt-2 w-full border-collapse text-sm">
                                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="p-2">Réalisations</th>
                                            <th class="p-2">Réussites</th>
                                            <th class="p-2">Difficultés</th>
                                            <th v-if="canEditField('employee')" class="w-8 p-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, i) in (readAnswers('employee')[field.key] || [])" :key="i"
                                            class="border-t border-gray-100">
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('employee')[field.key][i].realisations"
                                                    :disabled="!canEditField('employee')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('employee')[field.key][i].reussites"
                                                    :disabled="!canEditField('employee')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('employee')[field.key][i].difficultes"
                                                    :disabled="!canEditField('employee')" :class="inputCls" />
                                            </td>
                                            <td v-if="canEditField('employee')" class="p-1 text-center">
                                                <button type="button" class="text-xs text-red-600 hover:underline"
                                                    @click="removeRow('employee', field.key, i)">✕</button>
                                            </td>
                                        </tr>
                                        <tr v-if="!(readAnswers('employee')[field.key] || []).length">
                                            <td colspan="4" class="p-3 text-center text-xs italic text-gray-500">
                                                Aucune activité enregistrée
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </template>

                            <!-- Nouveaux objectifs -->
                            <template v-else-if="field.type === 'objectives_plan'">
                                <div class="flex items-center justify-between">
                                    <InputLabel :value="field.question" />
                                    <button v-if="canEditField('manager')" type="button"
                                        class="text-xs text-brand-primary hover:underline"
                                        @click="addRow('manager', field.key, { objectif: '', indicateurs: '', moyens: '', delais: '' })">
                                        + Ajouter un objectif
                                    </button>
                                </div>
                                <table class="mt-2 w-full border-collapse text-sm">
                                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="p-2">Objectif</th>
                                            <th class="p-2">Indicateurs</th>
                                            <th class="p-2">Moyens</th>
                                            <th class="w-32 p-2">Délais</th>
                                            <th v-if="canEditField('manager')" class="w-8 p-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, i) in (readAnswers('manager')[field.key] || [])" :key="i"
                                            class="border-t border-gray-100">
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('manager')[field.key][i].objectif"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('manager')[field.key][i].indicateurs"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('manager')[field.key][i].moyens"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('manager')[field.key][i].delais"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td v-if="canEditField('manager')" class="p-1 text-center">
                                                <button type="button" class="text-xs text-red-600 hover:underline"
                                                    @click="removeRow('manager', field.key, i)">✕</button>
                                            </td>
                                        </tr>
                                        <tr v-if="!(readAnswers('manager')[field.key] || []).length">
                                            <td colspan="5" class="p-3 text-center text-xs italic text-gray-500">
                                                Aucun objectif enregistré
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </template>

                            <!-- Grille de compétences -->
                            <template v-else-if="field.type === 'competency_grid'">
                                <InputLabel :value="field.question" />
                                <p v-if="field.hint" class="mb-2 text-xs italic text-gray-500">{{ field.hint }}</p>
                                <table class="w-full border-collapse text-sm">
                                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="p-2">Compétence attendue</th>
                                            <th class="w-44 p-2">Auto-évaluation (salarié)</th>
                                            <th class="p-2">Commentaires manager</th>
                                            <th class="p-2">Actions à mener</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(rowLabel, i) in field.rows" :key="i"
                                            class="border-t border-gray-100 align-top">
                                            <td class="p-2 text-xs text-gray-700 dark:text-gray-200">{{ rowLabel }}</td>
                                            <td class="p-1">
                                                <select :value="getGridCell('employee', field.key, i)"
                                                    @change="(e) => setGridCell('employee', field.key, i, e.target.value)"
                                                    :disabled="!canEditField('employee')" :class="inputCls">
                                                    <option value="">—</option>
                                                    <option v-for="opt in field.evaluation_options" :key="opt" :value="opt">
                                                        {{ opt }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td class="p-1">
                                                <input type="text"
                                                    :value="getGridCell('manager', field.key, i, 'comment')"
                                                    @input="(e) => setGridCell('manager', field.key, i, e.target.value, 'comment')"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text"
                                                    :value="getGridCell('manager', field.key, i, 'action')"
                                                    @input="(e) => setGridCell('manager', field.key, i, e.target.value, 'action')"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </template>
                        </div>
                    </div>
                </section>

                <!-- Boutons salarié -->
                <div v-if="employeeEditable" class="rounded-lg border border-amber-300 bg-amber-50 p-4">
                    <p class="mb-3 text-sm text-amber-800">
                        Vous êtes en train de préparer votre auto-évaluation. Enregistrez régulièrement en brouillon.
                        Une fois « Envoyer au manager » cliqué, vous ne pourrez plus modifier vos réponses.
                    </p>
                    <div class="flex flex-wrap justify-end gap-2">
                        <SecondaryButton :disabled="employeeForm.processing" @click="saveEmployee(false)">
                            Enregistrer brouillon
                        </SecondaryButton>
                        <PrimaryButton :disabled="employeeForm.processing" @click="saveEmployee(true)">
                            Envoyer au manager
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Boutons manager -->
                <div v-if="managerEditable" class="rounded-lg border border-indigo-300 bg-indigo-50 p-4">
                    <p class="mb-3 text-sm text-indigo-800">
                        Complétez votre partie. Quand l'entretien est prêt pour signature, cliquez sur « Finaliser ».
                    </p>
                    <div class="flex flex-wrap justify-end gap-2">
                        <SecondaryButton :disabled="managerForm.processing" @click="saveManager(false)">
                            Enregistrer brouillon
                        </SecondaryButton>
                        <PrimaryButton :disabled="managerForm.processing" @click="saveManager(true)">
                            Finaliser pour signature
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Signatures -->
                <section class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 border-b border-brand-primary/40 pb-2 text-base font-bold text-brand-primary">
                        Signatures
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
