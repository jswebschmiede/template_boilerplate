/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme');
const plugin = require('tailwindcss/plugin');
const { typography } = require('./tailwind/typography');

module.exports = {
    content: ['./src/media/*.{css,js}', './src/**/*.php', '../../joomla/modules/**/*.php'],
    theme: {
        screens: require('./tailwind/screens'),
        fontSize: require('./tailwind/fontsize'),

        extend: {
            colors: require('./tailwind/colors'),

            fontFamily: {
                sans: ['Noto Sans', ...defaultTheme.fontFamily.sans],
            },

            width: {
                'full-p-1': 'calc(100% - 2.5rem)',
                'full-p-2': 'calc(100% - 4rem)',
            },
            maxWidth: {
                content: '60rem', // 960px
                wide: '64rem', // 1024px
                big: '90rem', // 1440px
            },
            animation: {
                'button-pop': 'button-pop 0s ease-out',
                'slide-down': 'slide-down 0.35s ease-out',
            },
            keyframes: {
                'button-pop': {
                    '0%': {
                        transform: 'scale(.98)',
                    },
                    '40%': {
                        transform: 'scale(1.02)',
                    },
                    '100%': {
                        transform: 'scale(1)',
                    },
                },
                'slide-down': {
                    from: {
                        transform: 'translateY(-100%)',
                    },
                    to: {
                        transform: 'translateY(0)',
                    },
                },
            },

            typography: typography,
        },
    },
    safelist: ['btn', { pattern: /btn-(primary|secondary|outline)/ }],
    plugins: [
        plugin(function ({ addUtilities, theme }) {
            addUtilities({
                '.text-shadow': {
                    'text-shadow': '0 1px 1px rgba(0,0,0,0.3)',
                },
                '.shadow-inner-xl': {
                    'box-shadow': 'inset 0px 3px 23px rgba(0,0,0,0.25)',
                },
            });
        }),
        require('./tailwind/buttons'),
        require('@tailwindcss/forms', {
            strategy: 'base', // only generate global styles
        }),
        require('@tailwindcss/aspect-ratio'),
        require('@tailwindcss/typography'),
        require('tailwind-scrollbar')({ nocompatible: true }),
    ],
};
