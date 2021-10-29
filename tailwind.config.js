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
            transitionProperty: {
                height: 'height'
            },
            transitionProperty: {
                width: 'width'
            },
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'xxs': '.60rem',
            },
            zIndex: {
                '60': 60,
                '70': 70,
                '80': 80,
                '90': 90,
                '100': 100,
                'auto': 'auto',
            },
            inset: {
                '-1': '-1rem',
                '-2.5': '-2.5rem',
                '-3': '-3rem',
                '-3.5': '-3.5rem',
                '-4': '-4rem',
                '-5': '-5rem',
                '-5.5': '-5.5rem',
                '-6': '-6rem'
            },
            maxHeight: {
                '100-px': '100px',
                '200-px': '200px',
                '300-px': '300px',
                '400-px': '400px',
                '500-px': '500px',
                '600-px': '600px',
                '700-px': '700px',
                '800-px': '800px',
                '900-px': '900px'
            },
            maxWidth: {
                '100-px': '100px',
                '200-px': '200px',
                '300-px': '300px',
                '400-px': '400px',
                '500-px': '500px',
                '600-px': '600px',
                '700-px': '700px',
                '800-px': '800px',
                '900-px': '900px',
                '1000-px': '1000px',
                '1100-px': '1100px',
                '1200-px': '1200px',
                '1300-px': '1300px',
                '1400-px': '1400px',
            },
            height: theme => ({
                'screen-5': '5vh',
                'screen-8': '8vh',
                'screen-10': '10vh',
                'screen-20': '20vh',
                'screen-25': '25vh',
                'screen-30': '30vh',
                'screen-40': '40vh',
                'screen-50': '50vh',
                'screen-55': '55vh',
                'screen-60': '60vh',
                'screen-65': '65vh',
                'screen-70': '70vh',
                'screen-75': '75vh',
                'screen-80': '80vh',
                'screen-85': '85vh',
                'screen-90': '90vh',
                'screen-92': '92vh',
                'screen-95': '95vh',
                '7': '1.75rem',
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
                'screen-85': '85vw',
                'screen-88': '88vw',
                'screen-90': '90vw',
                'screen-95': '95vw',
                '112': '28rem',
                '128': '32rem',
            }),
            colors: {
                default: {
                    lightest: '#028ee6',
                    lighter: '#0275bd',
                    light: '#025283',
                    DEFAULT: '#003e64',
                    dark: '#022d46',
                    darker: '#001f31',
                },
                primary: {
                    lightest: '#e0ecff',
                    lighter: '#4e84da',
                    light: '#4a78c2',
                    DEFAULT: '#3f629c',
                    dark: '#37578a',
                    darker: '#2b4670',
                },
                secondary: {
                    lightest: '#fce4d4',
                    lighter: '#ffa366',
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
    plugins: [
        require('@tailwindcss/forms')
    ],
};
