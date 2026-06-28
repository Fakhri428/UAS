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
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ink: '#111111',
                paper: '#FFFCF2',
                brand: {
                    yellow: '#FFD93D',
                    pink: '#FF90E8',
                    sky: '#7DD3FC',
                    lime: '#A3E635',
                    purple: '#C4B5FD',
                    orange: '#FF8A4C',
                },
            },
            boxShadow: {
                'nb-sm': '2px 2px 0 0 #111111',
                nb: '4px 4px 0 0 #111111',
                'nb-lg': '6px 6px 0 0 #111111',
                'nb-xl': '8px 8px 0 0 #111111',
            },
        },
    },

    plugins: [forms, typography],
};
