import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    600: '#0284c7',
                    700: '#0369a1',
                },
            },
            transitionProperty: {
                'opacity': 'opacity',
            },
            transitionDuration: {
                '1000': '1000ms',
            },
        },
    },
    plugins: [],
    variants: {
        extend: {
            opacity: ['responsive', 'hover', 'focus', 'group-hover', 'group-focus'],
        }
    },
};
