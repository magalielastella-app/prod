<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    positions: { type: Array, default: () => [] },
});

const form = useForm({
    name: '',
    email: '',
    position: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Créer un compte" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nom complet" />
                <TextInput id="name" type="text" class="mt-1 block w-full"
                    v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email professionnel" />
                <TextInput id="email" type="email" class="mt-1 block w-full"
                    v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="position" value="Poste" />
                <select id="position" v-model="form.position" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="" disabled>— choisir votre poste —</option>
                    <option v-for="p in positions" :key="p" :value="p">{{ p }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.position" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Mot de passe" />
                <TextInput id="password" type="password" class="mt-1 block w-full"
                    v-model="form.password" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                    v-model="form.password_confirmation" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900">
                    Déjà inscrit ?
                </Link>
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    Créer mon compte
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
