import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    darkMode: 'class', // Opción para manejar el modo oscuro mediante una clase
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#D90537',
                primarylight: '#FEBFCE',
                secondary: '#0355B5',
                secondarylight: '#B9D9FE',
                gray1: '#9A9A9A',
                gray2: '#D9D9D9',
            },
        },
    },

    plugins: [forms, typography],
};
