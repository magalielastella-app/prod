<script setup>
/**
 * Modal générique avec slot body (contenu du formulaire) + titre.
 * Utilise le Modal de Breeze mais ajoute header + padding cohérents.
 */
import Modal from '@/Components/Modal.vue';

defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, required: true },
    maxWidth: { type: String, default: '2xl' },
});
defineEmits(['close']);
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" @close="$emit('close')">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ title }}</h3>
        </div>
        <div class="p-6">
            <slot />
        </div>
    </Modal>
</template>
