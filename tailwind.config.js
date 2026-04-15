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
            // Palette Cabinet Dentaire : teal principal + accents chauds/froids
            colors: {
                brand: {
                    // Teal = couleur principale (charte Cabinet de l'Obiou)
                    primary: '#115E59',
                    'primary-dark': '#0F4C47',   // fond du logo
                    'primary-light': '#14B8A6',

                    // Accents — chaque section/tuile a sa couleur
                    coral: '#FB7185',
                    violet: '#8B5CF6',
                    amber: '#F59E0B',
                    sky: '#38BDF8',
                    rose: '#F43F5E',
                    emerald: '#10B981',

                    // Neutres clairs
                    cream: '#F1F5F9',
                    beige: '#E2E8F0',
                    tan: '#94A3B8',
                    tertiary: '#ECFEFF',
                    dark: '#0F172A',
                },
            },
            backgroundImage: {
                'brand-gradient': 'linear-gradient(135deg, #0F766E 0%, #14B8A6 50%, #38BDF8 100%)',
                'brand-soft': 'linear-gradient(135deg, #ECFEFF 0%, #F1F5F9 100%)',
                'hero-splash': 'radial-gradient(ellipse at top left, rgba(20,184,166,0.15), transparent 60%), radial-gradient(ellipse at bottom right, rgba(251,113,133,0.12), transparent 60%)',
            },
            boxShadow: {
                'soft': '0 4px 20px -4px rgba(15, 23, 42, 0.08)',
                'glow-primary': '0 0 0 4px rgba(20, 184, 166, 0.15)',
                'glow-amber': '0 0 0 4px rgba(245, 158, 11, 0.15)',
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
