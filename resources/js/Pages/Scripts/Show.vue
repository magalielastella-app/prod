<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    script: { type: Object, required: true },
});

const editing = ref(false);

const form = useForm({
    title: props.script.title,
    description: props.script.description || '',
    sections: props.script.sections ? [...props.script.sections] : [{ title: '', content: '' }],
});

const addSection = () => {
    form.sections.push({ title: '', content: '' });
};

const removeSection = (index) => {
    if (form.sections.length > 1) {
        form.sections.splice(index, 1);
    }
};

const save = () => {
    form.post(route('scripts.update', props.script.id), {
        onSuccess: () => { editing.value = false; },
    });
};
</script>

<template>
    <Head :title="script.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('scripts.index')" class="text-sm text-brand-primary hover:underline mb-1 inline-block">&larr; Retour aux scripts</Link>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">{{ script.title }}</h2>
                </div>
                <SecondaryButton v-if="!editing" @click="editing = true">Modifier</SecondaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Mode édition -->
                <div v-if="editing" class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <form @submit.prevent="save" class="space-y-4">
                        <div>
                            <InputLabel value="Titre *" />
                            <TextInput v-model="form.title" class="mt-1 w-full" required />
                            <InputError :message="form.errors.title" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Description" />
                            <textarea v-model="form.description" rows="2"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        </div>

                        <!-- Sections dynamiques -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <InputLabel value="Sections" />
                                <button type="button" @click="addSection" class="text-sm text-brand-primary hover:underline">+ Ajouter une section</button>
                            </div>
                            <div v-for="(section, idx) in form.sections" :key="idx"
                                class="mb-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Section {{ idx + 1 }}</span>
                                    <button v-if="form.sections.length > 1" type="button" @click="removeSection(idx)"
                                        class="text-xs text-pink-600 hover:underline">Supprimer</button>
                                </div>
                                <TextInput v-model="section.title" placeholder="Titre de la section" class="w-full mb-2" />
                                <textarea v-model="section.content" rows="4" placeholder="Questions / contenu..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm" />
                            </div>
                            <InputError :message="form.errors.sections" class="mt-1" />
                        </div>

                        <div class="flex gap-3">
                            <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                            <SecondaryButton @click="editing = false">Annuler</SecondaryButton>
                        </div>
                    </form>
                </div>

                <!-- Mode lecture -->
                <div v-else class="space-y-4">
                    <div v-if="script.description" class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ script.description }}</p>
                    </div>

                    <div v-for="(section, idx) in script.sections" :key="idx"
                        class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="grid h-7 w-7 place-items-center rounded-full bg-brand-lavender text-xs font-bold text-violet-800">{{ idx + 1 }}</span>
                            {{ section.title }}
                        </h3>
                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap pl-9">{{ section.content }}</div>
                    </div>

                    <div v-if="!script.sections?.length" class="rounded-xl bg-white p-8 shadow-soft text-center dark:bg-gray-800">
                        <p class="text-sm italic text-gray-500">Aucune section définie</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
