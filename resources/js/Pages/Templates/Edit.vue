<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    template: { type: Object, required: true },
});

const form = useForm({
    label: props.template.label,
    header: (props.template.header || []).map((h) => ({ ...h })),
    sections: (props.template.sections || []).map((s) => ({
        title: s.title,
        fields: (s.fields || []).map((f) => ({ ...f })),
    })),
});

const submit = () => {
    form.post(route('templates.update', props.template.key), {
        preserveScroll: true,
    });
};

const fieldTypeName = (type) => {
    const m = {
        scale_10: 'Échelle 1-10',
        text: 'Texte court',
        textarea: 'Texte long',
        choice: 'Choix',
        objectives_review: 'Tableau objectifs (évaluation)',
        objectives_plan: 'Tableau objectifs (plan)',
        activities_table: 'Tableau activités',
        competency_grid: 'Grille de compétences',
    };
    return m[type] || type;
};

const ownerLabel = (owner) => {
    return owner === 'employee' ? 'Salarié' : owner === 'manager' ? 'Manager' : '—';
};
</script>

<template>
    <Head :title="`Modifier la trame — ${template.label}`" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <Link :href="route('templates.index')" class="text-sm text-brand-primary hover:underline">
                    ← Toutes les trames
                </Link>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Modifier la trame : {{ template.label }}
                </h2>
                <p class="mt-1 text-xs text-gray-500">
                    Clé : <code class="font-mono">{{ template.key }}</code> —
                    Vous pouvez modifier le titre de la trame, les titres des sections et les libellés des questions.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit">

                    <!-- Nom de la trame -->
                    <div class="rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800">
                        <InputLabel value="Nom de la trame" />
                        <TextInput v-model="form.label" type="text" class="mt-1 block w-full max-w-md" required />
                        <InputError :message="form.errors.label" class="mt-2" />
                    </div>

                    <!-- Champs d'entête -->
                    <div v-if="form.header.length" class="mt-6 rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800">
                        <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-gray-100">
                            Champs d'entête
                        </h3>
                        <div class="space-y-3">
                            <div v-for="(h, i) in form.header" :key="i"
                                class="grid grid-cols-1 gap-2 rounded border border-gray-200 p-3 sm:grid-cols-12 dark:border-gray-700">
                                <div class="sm:col-span-4">
                                    <InputLabel value="Libellé" />
                                    <TextInput v-model="h.label" type="text" class="mt-1 block w-full" />
                                </div>
                                <div class="sm:col-span-4">
                                    <InputLabel value="Clé" />
                                    <TextInput :model-value="h.key" type="text" class="mt-1 block w-full bg-gray-50" disabled />
                                </div>
                                <div class="sm:col-span-4">
                                    <InputLabel value="Rempli par" />
                                    <TextInput :model-value="ownerLabel(h.owner)" type="text" class="mt-1 block w-full bg-gray-50" disabled />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sections -->
                    <div v-for="(section, si) in form.sections" :key="si"
                        class="mt-6 rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800">
                        <div class="mb-4">
                            <InputLabel :value="`Titre de la section ${si + 1}`" />
                            <TextInput v-model="section.title" type="text" class="mt-1 block w-full" required />
                            <InputError :message="form.errors[`sections.${si}.title`]" class="mt-2" />
                        </div>

                        <div class="space-y-3">
                            <div v-for="(field, fi) in section.fields" :key="fi"
                                class="rounded border border-gray-200 p-3 dark:border-gray-700">
                                <div class="mb-2 flex items-center justify-between text-xs text-gray-500">
                                    <span class="flex items-center gap-2">
                                        <span class="rounded bg-gray-100 px-2 py-0.5 font-mono dark:bg-gray-700">{{ fieldTypeName(field.type) }}</span>
                                        <span class="rounded bg-brand-tertiary px-2 py-0.5 text-brand-primary">{{ ownerLabel(field.owner) }}</span>
                                    </span>
                                    <span class="font-mono text-gray-400">{{ field.key }}</span>
                                </div>

                                <!-- Question / libellé -->
                                <div v-if="field.question !== undefined">
                                    <InputLabel value="Question / libellé" />
                                    <textarea v-model="field.question" rows="2"
                                        class="mt-1 block w-full rounded border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
                                    <InputError :message="form.errors[`sections.${si}.fields.${fi}.question`]" class="mt-2" />
                                </div>

                                <!-- Hint -->
                                <div v-if="field.hint !== undefined" class="mt-2">
                                    <InputLabel value="Note d'aide" />
                                    <TextInput v-model="field.hint" type="text" class="mt-1 block w-full" />
                                </div>

                                <!-- Lignes de la grille de compétences -->
                                <div v-if="field.rows" class="mt-2">
                                    <InputLabel value="Lignes de la grille" />
                                    <div class="mt-1 space-y-1">
                                        <TextInput v-for="(row, ri) in field.rows" :key="ri"
                                            v-model="field.rows[ri]" type="text" class="block w-full" />
                                    </div>
                                </div>

                                <!-- Options d'évaluation -->
                                <div v-if="field.evaluation_options" class="mt-2">
                                    <InputLabel value="Options d'évaluation" />
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        <TextInput v-for="(opt, oi) in field.evaluation_options" :key="oi"
                                            v-model="field.evaluation_options[oi]" type="text" class="w-40" />
                                    </div>
                                </div>

                                <!-- Options de choix -->
                                <div v-if="field.options" class="mt-2">
                                    <InputLabel value="Options de choix" />
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        <TextInput v-for="(opt, oi) in field.options" :key="oi"
                                            v-model="field.options[oi]" type="text" class="w-40" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barre d'actions -->
                    <div class="mt-6 flex justify-end gap-2">
                        <Link :href="route('templates.index')">
                            <SecondaryButton type="button">Annuler</SecondaryButton>
                        </Link>
                        <PrimaryButton :disabled="form.processing">
                            Enregistrer les modifications
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
