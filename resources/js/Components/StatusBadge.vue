<script setup>
defineProps({
    label: { type: String, required: true },
    cls: { type: String, default: 'ok' }, // ok | warn | danger | info
});
const colors = {
    ok: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
    warn: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
    danger: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
    info: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200',
};
</script>

<template>
    <span :class="['inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide', colors[cls] || colors.ok]">
        {{ label }}
    </span>
</template>
