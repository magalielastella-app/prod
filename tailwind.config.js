import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // Palette Cabinet Dentaire : teal/bleu doux (santé) + neutres clairs
            colors: {
                brand: {
                    // Teal = couleur principale/primaire
                    primary: '#0F766E',       // teal principal
                    'primary-dark': '#115E59',
                    'primary-light': '#14B8A6',
                    accent: '#38BDF8',        // bleu ciel (accent)
                    cream: '#F1F5F9',         // fond général (slate-100)
                    beige: '#E2E8F0',         // bordures douces (slate-200)
                    tan: '#94A3B8',           // bordures contrastées (slate-400)
                    tertiary: '#ECFEFF',      // teinte très claire (cartes)
                    dark: '#0F172A',          // texte principal (slate-900)
                },
            },
        },
    },

    plugins: [forms],
};
