/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        primary: {
          50:  '#e6eff7',
          100: '#c1d6eb',
          200: '#9bbde0',
          300: '#75a3d4',
          400: '#4f8ac9',
          500: '#053C6B', // Primary base
          600: '#04355f',
          700: '#032b4f',
          800: '#02213f',
          900: '#01172f',
          950: '#000d1f',
        },              
        secondary: {
          50: '#f7f8f8',
          100: '#edeef1',
          200: '#d8dbdf',
          300: '#b6bac3',
          400: '#8e95a2',
          500: '#6b7280',
          600: '#5b616e',
          700: '#4a4e5a',
          800: '#40444c',
          900: '#383a42',
          950: '#25272c',
        },
        lightOrange: {
          50: '#fff4ed',
          100: '#ffe2d3',
          200: '#ffc0a6',
          300: '#ff9d78',
          400: '#ff7a4a',
          500: '#F85606',
          600: '#d14705',
          700: '#a83804',
          800: '#7f2a03',
          900: '#551c02',
          950: '#2d0e01',
        },
      },
      animation: {
        fall: 'fall 10s linear infinite',
      },
      keyframes: {
        fall: {
          '0%': { transform: 'translateY(-100%)', opacity: '0' },
          '25%': { opacity: '1' },
          '100%': { transform: 'translateY(100vh)', opacity: '0' },
        },
      },
    },
  },
  plugins: [],
};
