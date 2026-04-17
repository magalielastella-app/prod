<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: { type: Array, default: () => [] },
    managers: { type: Array, default: () => [] },
    positions: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
});

const showingModal = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    role: 'employee',
    position: '',
    department: '',
    manager_id: '',
    hired_on: '',
    password: '',
    password_confirmation: '',
});

const openCreate = () => {
    editingUser.value = null;
    form.reset();
    form.role = 'employee';
    showingModal.value = true;
};

const openEdit = (user) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.position = user.position || '';
    form.department = user.department || '';
    form.manager_id = user.manager_id || '';
    form.hired_on = user.hired_on || '';
    form.password = '';
    form.password_confirmation = '';
    showingModal.value = true;
};

const submit = () => {
    // Toutes les mutations en POST (le proxy Render bloque PUT/DELETE).
    const url = editingUser.value
        ? route('team.update', editingUser.value.id)
        : route('team.store');
    form.post(url, {
        onSuccess: () => { showingModal.value = false; form.reset(); },
    });
};

const destroy = (user) => {
    if (!confirm(`Supprimer ${user.name} ? Les entretiens associés seront également supprimés.`)) return;
    useForm({}).post(route('team.destroy', user.id));
};

const roleLabel = (value) => props.roles.find((r) => r.value === value)?.label || value;
</script>

<template>
    <Head title="Équipe" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Équipe du cabinet
                </h2>
                <PrimaryButton @click="openCreate">+ Ajouter un membre</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-4 py-3">Nom</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Poste</th>
                                <th class="px-4 py-3">Rôle</th>
                                <th class="px-4 py-3">Manager</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="u in users" :key="u.id" class="text-sm text-gray-700 dark:text-gray-200">
                                <td class="px-4 py-3 font-medium">{{ u.name }}</td>
                                <td class="px-4 py-3">{{ u.email }}</td>
                                <td class="px-4 py-3">{{ u.position || '—' }}</td>
                                <td class="px-4 py-3">{{ roleLabel(u.role) }}</td>
                                <td class="px-4 py-3">
                                    {{ managers.find((m) => m.id === u.manager_id)?.name || '—' }}
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <button class="text-brand-primary hover:underline" @click="openEdit(u)">
                                        Modifier
                                    </button>
                                    <button class="text-red-600 hover:underline" @click="destroy(u)">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!users.length">
                                <td colspan="6" class="px-4 py-10 text-center text-sm italic text-gray-500">
                                    Aucun membre enregistré.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal :show="showingModal" max-width="2xl" @close="showingModal = false">
            <form class="p-6" @submit.prevent="submit">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ editingUser ? 'Modifier le membre' : 'Nouveau membre' }}
                </h3>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <InputLabel value="Nom complet" />
                        <TextInput v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Email" />
                        <TextInput v-model="form.email" type="email" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Date d'embauche" />
                        <TextInput v-model="form.hired_on" type="date" class="mt-1 block w-full" />
                        <InputError :message="form.errors.hired_on" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Poste" />
                        <select v-model="form.position" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="" disabled>— choisir —</option>
                            <option v-for="p in positions" :key="p" :value="p">{{ p }}</option>
                        </select>
                        <InputError :message="form.errors.position" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Rôle applicatif" />
                        <select v-model="form.role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
                        </select>
                        <InputError :message="form.errors.role" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Manager" />
                        <select v-model="form.manager_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">— aucun —</option>
                            <option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                        <InputError :message="form.errors.manager_id" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Service / pôle" />
                        <TextInput v-model="form.department" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.department" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel :value="editingUser ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe'" />
                        <TextInput v-model="form.password" type="password" class="mt-1 block w-full"
                            :required="!editingUser" autocomplete="new-password" />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Confirmer mot de passe" />
                        <TextInput v-model="form.password_confirmation" type="password" class="mt-1 block w-full"
                            :required="!editingUser || !!form.password" autocomplete="new-password" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <SecondaryButton type="button" @click="showingModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ editingUser ? 'Enregistrer' : 'Créer le membre' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
