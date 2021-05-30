const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')

module.exports = {
    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            height: theme => ({
                'screen-10': '10vh',
                'screen-20': '20vh',
                'screen-25': '25vh',
                'screen-30': '30vh',
                'screen-40': '40vh',
                'screen-50': '50vh',
                'screen-60': '60vh',
                'screen-70': '70vh',
                'screen-75': '75vh',
                'screen-80': '80vh',
                'screen-90': '90vh',
            }),
            width: theme => ({
                'screen-10': '10vw',
                'screen-20': '20vw',
                'screen-25': '25vw',
                'screen-30': '30vw',
                'screen-40': '40vw',
                'screen-50': '50vw',
                'screen-60': '60vw',
                'screen-70': '70vw',
                'screen-75': '75vw',
                'screen-80': '80vw',
                'screen-90': '90vw',
            }),
            colors: {
                default: {
                    light: '#64748B',
                    DEFAULT: '#475569',
                    dark: '#334155',
                },
                primary: {
                    light: '#025ba3',
                    DEFAULT: '#015091',
                    dark: '#124772',
                },
                secondary: {
                    light: '#f7863b',
                    DEFAULT: '#ce6621',
                    dark: '#c25208',
                },
                success: {
                    light: '#5fa863',
                    DEFAULT: '#518d54',
                    dark: '#447546',
                },
                danger: {
                    light: '#b85654',
                    DEFAULT: '#a14f4e',
                    dark: '#8d4746',
                },
            },
        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
