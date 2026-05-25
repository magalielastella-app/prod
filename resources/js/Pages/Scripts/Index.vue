<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    scripts: { type: Object, required: true },
});

const showModal = ref(false);

const form = useForm({
    title: '',
    description: '',
    sections: [{ title: '', content: '' }],
});

const addSection = () => {
    form.sections.push({ title: '', content: '' });
};

const removeSection = (index) => {
    if (form.sections.length > 1) {
        form.sections.splice(index, 1);
    }
};

const submit = () => {
    form.post(route('scripts.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            form.sections = [{ title: '', content: '' }];
        },
    });
};

const destroy = (id) => {
    if (confirm('Supprimer ce script ?')) {
        router.post(route('scripts.destroy', id));
    }
};
</script>

<template>
    <Head title="Scripts d'entretien" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">Scripts d'entretien</h2>
                <PrimaryButton @click="showModal = true">+ Créer un script</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="s in scripts.data" :key="s.id"
                        class="rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800 transition hover:shadow-md">
                        <div class="flex items-start justify-between mb-3">
                            <Link :href="route('scripts.show', s.id)" class="text-base font-semibold text-gray-900 dark:text-gray-100 hover:text-brand-primary">
                                {{ s.title }}
                            </Link>
                        </div>
                        <p v-if="s.description" class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">{{ s.description }}</p>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ s.sections?.length || 0 }} section(s)</span>
                            <span>{{ new Date(s.created_at).toLocaleDateString('fr-FR') }}</span>
                        </div>
                        <div class="mt-3 flex justify-end gap-2 text-sm">
                            <Link :href="route('scripts.show', s.id)" class="text-brand-primary hover:underline">Voir</Link>
                            <button @click="destroy(s.id)" class="text-pink-600 hover:underline">Supprimer</button>
                        </div>
                    </div>
                </div>

                <div v-if="!scripts.data.length" class="rounded-xl bg-white p-8 shadow-soft text-center dark:bg-gray-800 mt-4">
                    <p class="text-sm italic text-gray-500">Aucun script créé</p>
                </div>

                <!-- Pagination -->
                <div v-if="scripts.links && scripts.links.length > 3" class="mt-6 flex justify-center gap-1">
                    <template v-for="link in scripts.links" :key="link.label">
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Créer un script d'entretien</h3>
                <form @submit.prevent="submit" class="space-y-4">
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
                            <InputLabel value="Sections *" />
                            <button type="button" @click="addSection" class="text-sm text-brand-primary hover:underline">+ Ajouter une section</button>
                        </div>
                        <div v-for="(section, idx) in form.sections" :key="idx"
                            class="mb-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-gray-500">Section {{ idx + 1 }}</span>
                                <button v-if="form.sections.length > 1" type="button" @click="removeSection(idx)"
                                    class="text-xs text-pink-600 hover:underline">Supprimer</button>
                            </div>
                            <TextInput v-model="section.title" placeholder="Titre de la section" class="w-full mb-2" />
                            <textarea v-model="section.content" rows="3" placeholder="Questions / contenu..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm" />
                        </div>
                        <InputError :message="form.errors.sections" class="mt-1" />
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
