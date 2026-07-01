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
          50: '#4a4a4a',
          100: '#3d3d3d',
          200: '#333333',
          300: '#2d2d2d',
          400: '#262626',
          500: '#1e1e1e',
          600: '#1a1a1a',
          700: '#151515',
          800: '#111111',
          900: '#0d0d0d',
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
