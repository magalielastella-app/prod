<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Affiche un toast éphémère à partir des messages flash (success/error)
 * partagés par HandleInertiaRequests.
 */
const page = usePage();
const visible = ref(false);
const message = ref('');
const type = ref('success');
let timeout = null;

const flash = computed(() => page.props.flash || {});

watch(
    flash,
    (f) => {
        const msg = f.success || f.error;
        if (!msg) return;
        message.value = msg;
        type.value = f.error ? 'error' : 'success';
        visible.value = true;
        clearTimeout(timeout);
        timeout = setTimeout(() => (visible.value = false), 3000);
    },
    { deep: true, immediate: true }
);
</script>

<template>
    <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 translate-y-2"
        leave-active-class="transition duration-150" leave-to-class="opacity-0 translate-y-2">
        <div v-if="visible"
            :class="[type === 'error' ? 'bg-red-600' : 'bg-emerald-600', 'fixed bottom-6 right-6 z-50 rounded-lg px-5 py-3 text-sm font-medium text-white shadow-lg']">
            {{ message }}
        </div>
    </Transition>
</template>
