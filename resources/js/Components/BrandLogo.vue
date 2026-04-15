<script setup>
/**
 * Logo Cabinet Dentaire de l'Obiou.
 * Charte : fond teal très foncé, symbole + typo en blanc.
 *
 * Props :
 *  - variant : "full" (symbole + texte) | "mark" (symbole seul)
 *  - size    : "sm" | "md" | "lg" (ajuste uniquement la hauteur)
 */
defineProps({
    variant: { type: String, default: 'full' },
    size: { type: String, default: 'md' },
});

// Mapping des tailles — appliqué au wrapper externe.
const sizes = {
    sm: 'h-9',
    md: 'h-12',
    lg: 'h-16',
    xl: 'h-24',
};
</script>

<template>
    <div :class="['inline-flex items-stretch overflow-hidden rounded-lg bg-brand-primary-dark text-white shadow-sm ring-1 ring-white/10', sizes[size] || sizes.md]">
        <!-- Symbole : silhouette de dent + montagnes (inspiré du logo Cabinet de l'Obiou) -->
        <div class="grid aspect-square place-items-center p-1.5">
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-full w-full">
                <!-- Silhouette de dent (contour arrondi avec petit creux au sommet) -->
                <path
                    d="M12 6 C 10 6 8 8 8 10 V 38 C 8 40 10 42 12 42 H 26 C 28 42 30 40 30 38 V 24 C 30 22 29 21 27 20 C 25 19 24 18 24 15 V 10 C 24 8 22 6 20 6 H 12 Z"
                    stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" fill="none" />
                <!-- Montagnes à l'intérieur -->
                <polyline
                    points="12,32 16,22 20,28 24,18 28,30 30,28"
                    stroke="currentColor" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" fill="none" />
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
