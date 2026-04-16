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
            // Palette pastelle — Cabinet Dentaire de l'Obiou.
            // Le teal du logo reste l'identité ; les fonds, statuts et
            // tuiles passent en pastel doux pour un rendu apaisant.
            colors: {
                brand: {
                    // Teal — actions, liens, boutons primaires
                    primary: '#14B8A6',
                    'primary-dark': '#0F766E',
                    'primary-light': '#5EEAD4',
                    'primary-bg': '#0F4C47',     // fond logo (sombre, conservé)

                    // Pastels d'accent — pour cartes, status, sections
                    mint: '#A7F3D0',
                    peach: '#FED7AA',
                    rose: '#FBCFE8',
                    lavender: '#E9D5FF',
                    butter: '#FEF3C7',
                    sky: '#BAE6FD',
                    coral: '#FECACA',
                    ice: '#CFFAFE',

                    // Neutres très clairs
                    cream: '#FAFAF9',            // fond général
                    beige: '#F1F5F9',            // séparateurs doux
                    tan: '#CBD5E1',              // bordures contrastées
                    tertiary: '#F0FDFA',         // teinte très douce mint
                    dark: '#0F172A',             // texte principal
                },
            },
            backgroundImage: {
                // Gradient principal — pastel mint → ciel → lavande, gardant un peu
                // de teal pour la signature visuelle. Texte foncé recommandé.
                'brand-gradient': 'linear-gradient(135deg, #5EEAD4 0%, #BAE6FD 50%, #DDD6FE 100%)',
                'brand-soft': 'linear-gradient(135deg, #F0FDFA 0%, #FAFAF9 100%)',
                'hero-splash': 'radial-gradient(ellipse at top left, rgba(94,234,212,0.25), transparent 60%), radial-gradient(ellipse at bottom right, rgba(251,207,232,0.22), transparent 60%)',
            },
            boxShadow: {
                'soft': '0 4px 20px -4px rgba(15, 23, 42, 0.06)',
                'glow-primary': '0 0 0 4px rgba(94, 234, 212, 0.20)',
                'glow-amber': '0 0 0 4px rgba(254, 215, 170, 0.30)',
            },
            animation: {
                'soft-pulse': 'soft-pulse 2.5s ease-in-out infinite',
                'fade-in': 'fade-in 0.4s ease-out',
            },
            keyframes: {
                'soft-pulse': {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.75' },
                },
                'fade-in': {
                    '0%': { opacity: '0', transform: 'translateY(4px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
