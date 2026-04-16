<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = page.props.auth.user?.name || '';

const form = useForm({
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('password.force-change.update'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Changer votre mot de passe" />

        <div class="mb-4 text-center">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Bienvenue, {{ userName }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Pour votre sécurité, veuillez choisir un mot de passe personnel avant d'accéder
                à l'application. Votre mot de passe temporaire ne sera plus valide.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Nouveau mot de passe" />
                <TextInput id="password" type="password" class="mt-1 block w-full"
                    v-model="form.password" required autofocus autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                    v-model="form.password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="mt-6">
                <PrimaryButton class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    Enregistrer et accéder à l'application
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
