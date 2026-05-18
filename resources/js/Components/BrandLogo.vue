<script setup>
/**
 * Logo Cabinet Dentaire de l'Obiou — emoji « presse-papier » sur fond teal.
 *
 * Props :
 *  - variant : "full" (emoji + texte) | "mark" (emoji seul)
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

// Tailles d'émoji alignées sur la hauteur du conteneur.
const emojiSizes = {
    sm: 'text-xl',
    md: 'text-2xl',
    lg: 'text-3xl',
    xl: 'text-5xl',
};
</script>

<template>
    <div :class="['inline-flex items-stretch overflow-hidden rounded-lg bg-brand-primary-bg text-white shadow-sm ring-1 ring-white/10', sizes[size] || sizes.md]">
        <!-- Emoji professionnel (cible / objectifs) -->
        <div class="grid aspect-square place-items-center">
            <span :class="['leading-none', emojiSizes[size] || emojiSizes.md]" aria-hidden="true">
                🎯
            </span>
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
