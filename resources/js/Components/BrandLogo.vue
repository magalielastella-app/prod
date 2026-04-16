<script setup>
/**
 * Logo Cabinet Dentaire de l'Obiou — dent stylisée.
 *
 * Props :
 *  - variant : "full" (dent + texte) | "mark" (dent seule)
 *  - size    : "sm" | "md" | "lg" | "xl"
 */
defineProps({
    variant: { type: String, default: 'full' },
    size: { type: String, default: 'md' },
});

const sizes = {
    sm: 'h-9',
    md: 'h-12',
    lg: 'h-16',
    xl: 'h-24',
};
</script>

<template>
    <div :class="['inline-flex items-stretch overflow-hidden rounded-lg bg-brand-primary-bg text-white shadow-sm ring-1 ring-white/10', sizes[size] || sizes.md]">
        <!-- Dent stylisée -->
        <div class="grid aspect-square place-items-center p-1.5">
            <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-full w-full drop-shadow-sm">
                <path
                    d="M32 6
                       C 24 6  18 10  17 18
                       C 16 24  18 28  19 32
                       C 20 36  20.5 40  20 46
                       C 19.5 50  19 54  21 57
                       C 23 60  25 58  26 54
                       C 27 50  27.5 46  28 42
                       C 28.5 40  30 38  32 38
                       C 34 38  35.5 40  36 42
                       C 36.5 46  37 50  38 54
                       C 39 58  41 60  43 57
                       C 45 54  44.5 50  44 46
                       C 43.5 40  44 36  45 32
                       C 46 28  48 24  47 18
                       C 46 10  40 6  32 6 Z"
                    fill="white"
                    opacity="0.95"
                />
                <path
                    d="M25 16 C 28 14  36 14  39 16"
                    stroke="rgba(255,255,255,0.4)"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    fill="none"
                />
            </svg>
        </div>

        <!-- Texte (partie « full » seulement) -->
        <div v-if="variant === 'full'" class="flex items-center border-l border-white/20 px-3 pr-4">
            <div class="font-bold uppercase leading-tight tracking-wide"
                :class="size === 'sm' ? 'text-[10px]' : (size === 'lg' || size === 'xl') ? 'text-base' : 'text-xs'">
                Cabinet<br />
                Dentaire<br />
                de l'Obiou
            </div>
        </div>
    </div>
</template>
