<script setup>
defineProps({
    label: { type: String, required: true },
    cls: { type: String, default: 'ok' }, // ok | warn | danger | info
});
const colors = {
    ok: 'bg-emerald-100 text-emerald-800 ring-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-200 dark:ring-emerald-700/40',
    warn: 'bg-amber-100 text-amber-800 ring-amber-200 dark:bg-amber-900/40 dark:text-amber-200 dark:ring-amber-700/40',
    danger: 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/40 dark:text-red-200 dark:ring-red-700/40',
    info: 'bg-sky-100 text-sky-800 ring-sky-200 dark:bg-sky-900/40 dark:text-sky-200 dark:ring-sky-700/40',
};
const dotColors = {
    ok: 'bg-emerald-500',
    warn: 'bg-amber-500 animate-soft-pulse',
    danger: 'bg-red-500 animate-soft-pulse',
    info: 'bg-sky-500',
};
</script>

<template>
    <span :class="['inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold uppercase tracking-wide ring-1', colors[cls] || colors.ok]">
        <span :class="['h-1.5 w-1.5 rounded-full', dotColors[cls] || dotColors.ok]" />
        {{ label }}
    </span>
</template>
