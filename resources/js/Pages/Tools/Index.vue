<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataModal from '@/Components/DataModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    company: { type: Object, required: true },
    documents: { type: Array, required: true },
    categories: { type: Array, required: true },
});

// ----- Infos société -----
const companyForm = useForm({ ...props.company });
function saveCompany() {
    companyForm.put(route('company.update'), { preserveScroll: true });
}

// ----- Documents -----
const activeCategory = ref('all');
const filteredDocs = () =>
    activeCategory.value === 'all'
        ? props.documents
        : props.documents.filter(d => d.category === activeCategory.value);

const showDocModal = ref(false);
const editingDoc = ref(null);
const docForm = useForm({
    title: '',
    category: 'Fiche technique',
    description: '',
    file: null,
});

function openDocCreate() {
    editingDoc.value = null;
    docForm.reset();
    docForm.category = 'Fiche technique';
    docForm.clearErrors();
    showDocModal.value = true;
}
function openDocEdit(doc) {
    editingDoc.value = doc.id;
    docForm.clearErrors();
    docForm.title = doc.title;
    docForm.category = doc.category;
    docForm.description = doc.description || '';
    docForm.file = null;
    showDocModal.value = true;
}
function submitDoc() {
    const opts = {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => (showDocModal.value = false),
    };
    if (editingDoc.value) {
        docForm.put(route('documents.update', editingDoc.value), opts);
    } else {
        docForm.post(route('documents.store'), opts);
    }
}
function deleteDoc(doc) {
    if (!confirm(`Supprimer "${doc.title}" ?`)) return;
    router.delete(route('documents.destroy', doc.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Outils" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Outils — Informations & Documents
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Société -->
                <section class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            Informations société
                        </h3>
                    </div>
                    <form @submit.prevent="saveCompany" class="grid grid-cols-1 gap-4 p-5 md:grid-cols-3">
                        <div class="md:col-span-3">
                            <InputLabel value="Nom commercial *" />
                            <TextInput v-model="companyForm.name" required class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Forme juridique" />
                            <TextInput v-model="companyForm.legal_form" placeholder="SARL, SAS..." class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="SIRET" />
                            <TextInput v-model="companyForm.siret" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="N° TVA" />
                            <TextInput v-model="companyForm.vat_number" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="RCS" />
                            <TextInput v-model="companyForm.rcs" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Code APE" />
                            <TextInput v-model="companyForm.ape_code" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Capital (€)" />
                            <TextInput v-model="companyForm.capital" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div class="md:col-span-3">
                            <InputLabel value="Adresse" />
                            <TextInput v-model="companyForm.address" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Code postal" />
                            <TextInput v-model="companyForm.postal_code" class="mt-1 block w-full" />
                        </div>
                        <div class="md:col-span-2">
                            <InputLabel value="Ville" />
                            <TextInput v-model="companyForm.city" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Téléphone" />
                            <TextInput v-model="companyForm.phone" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Email" />
                            <TextInput v-model="companyForm.email" type="email" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Site web" />
                            <TextInput v-model="companyForm.website" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Gérant / Responsable" />
                            <TextInput v-model="companyForm.manager_name" class="mt-1 block w-full" />
                        </div>
                        <div class="md:col-span-2">
                            <InputLabel value="Horaires d'ouverture" />
                            <TextInput v-model="companyForm.opening_hours" class="mt-1 block w-full" />
                        </div>
                        <div class="md:col-span-3">
                            <InputLabel value="Notes" />
                            <textarea v-model="companyForm.notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                        </div>
                        <div class="md:col-span-3 flex justify-end">
                            <PrimaryButton :disabled="companyForm.processing">Enregistrer</PrimaryButton>
                        </div>
                    </form>
                </section>

                <!-- Documents -->
                <section class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            Bibliothèque de documents
                        </h3>
                        <PrimaryButton @click="openDocCreate">+ Ajouter un document</PrimaryButton>
                    </div>
                    <div class="flex flex-wrap gap-2 border-b border-gray-200 bg-gray-50 px-5 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                        <button @click="activeCategory = 'all'"
                            :class="['rounded-full px-3 py-1 text-xs font-medium', activeCategory === 'all' ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 dark:bg-gray-800 dark:text-gray-300']">
                            Toutes
                        </button>
                        <button v-for="c in categories" :key="c" @click="activeCategory = c"
                            :class="['rounded-full px-3 py-1 text-xs font-medium', activeCategory === c ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 dark:bg-gray-800 dark:text-gray-300']">
                            {{ c }}
                        </button>
                    </div>
                    <div class="p-5">
                        <div v-if="!filteredDocs().length" class="py-8 text-center italic text-gray-500">
                            Aucun document dans cette catégorie.
                        </div>
                        <div v-else class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                            <div v-for="d in filteredDocs()" :key="d.id"
                                class="flex flex-col rounded-md border border-gray-200 p-4 dark:border-gray-700">
                                <div class="mb-2 flex items-start justify-between gap-2">
                                    <div>
                                        <span class="inline-block rounded-full bg-brand-tertiary/80 px-2 py-0.5 text-[11px] font-semibold text-brand-primary">
                                            {{ d.category }}
                                        </span>
                                        <h4 class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ d.title }}</h4>
                                    </div>
                                </div>
                                <p v-if="d.description" class="mb-2 text-xs text-gray-600 dark:text-gray-300">{{ d.description }}</p>
                                <div class="mt-auto flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ d.original_name }} — {{ d.size_human }}</span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a :href="route('documents.download', d.id)"
                                        class="rounded border border-brand-primary px-2 py-1 text-xs font-medium text-brand-primary hover:bg-brand-primary hover:text-white">
                                        Télécharger
                                    </a>
                                    <SecondaryButton @click="openDocEdit(d)" class="!px-2 !py-1 text-xs">Modifier</SecondaryButton>
                                    <DangerButton @click="deleteDoc(d)" class="!px-2 !py-1 text-xs">Suppr</DangerButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- Modal document -->
        <DataModal :show="showDocModal" :title="editingDoc ? 'Modifier le document' : 'Ajouter un document'" @close="showDocModal = false">
            <form @submit.prevent="submitDoc" class="space-y-4">
                <div>
                    <InputLabel value="Titre *" />
                    <TextInput v-model="docForm.title" required class="mt-1 block w-full" />
                    <InputError :message="docForm.errors.title" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Catégorie *" />
                    <select v-model="docForm.category" required
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                    </select>
                </div>
                <div>
                    <InputLabel value="Description" />
                    <textarea v-model="docForm.description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                </div>
                <div v-if="!editingDoc">
                    <InputLabel value="Fichier *" />
                    <input type="file" @input="docForm.file = $event.target.files[0]" required
                        class="mt-1 block w-full rounded-md border border-gray-300 text-sm file:mr-3 file:rounded file:border-0 file:bg-brand-primary file:px-3 file:py-2 file:text-white dark:border-gray-700" />
                    <InputError :message="docForm.errors.file" class="mt-1" />
                    <p class="mt-1 text-xs text-gray-500">PDF, image ou fichier bureautique (max 20 Mo).</p>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <SecondaryButton @click="showDocModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="docForm.processing">Enregistrer</PrimaryButton>
                </div>
            </form>
        </DataModal>
    </AuthenticatedLayout>
</template>
