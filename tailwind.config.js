/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#e6fcf5',
          100: '#c3fae8',
          200: '#96f2d7',
          300: '#63e6be',
          400: '#38d9a9',
          500: '#20c997',
          600: '#12b886',
          700: '#0ca678',
          800: '#099268',
          900: '#087f5b',
        },
        dark: {
          50: '#535a65',
          100: '#454c56',
          200: '#3a4149',
          300: '#31373f',
          400: '#282e35',
          500: '#222830',
          600: '#1c2128',
          700: '#181c22',
          800: '#14181e',
          900: '#10131a',
        },
        accent: {
          green: '#20c997',
          gold: '#ffd43b',
          teal: '#0ca678',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
