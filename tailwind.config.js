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
            // Palette Smash You : beige/crème (fond) + rouge bordeaux (accent)
            colors: {
                brand: {
                    // Rouge bordeaux = couleur principale/primaire
                    primary: '#A02A2A',       // rouge principal
                    'primary-dark': '#7F1F1F',
                    'primary-light': '#C4453E',
                    // Accent (rouge vif/brique)
                    accent: '#D64545',
                    // Beige/crème = fonds et cartes
                    cream: '#F7EFDF',         // crème principale (fond app)
                    beige: '#EADBC0',         // beige moyen
                    tan: '#D9B48F',           // beige foncé (bordures)
                    tertiary: '#FBF5E9',      // teinte claire (cartes)
                    dark: '#2D1F1A',          // brun très foncé (texte)
                },
            },
        },
    },

    plugins: [forms],
};
