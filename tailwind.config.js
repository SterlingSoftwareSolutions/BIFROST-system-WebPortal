/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontSize: {
        'xs': ['0.875rem', { lineHeight: '1.25rem' }],
        'sm': ['1rem', { lineHeight: '1.5rem' }],
        'base': ['1.125rem', { lineHeight: '1.75rem' }],
        'lg': ['1.25rem', { lineHeight: '1.75rem' }],
        'xl': ['1.375rem', { lineHeight: '2rem' }],
        '2xl': ['1.625rem', { lineHeight: '2.25rem' }],
        '3xl': ['2rem', { lineHeight: '2.5rem' }],
        '4xl': ['2.375rem', { lineHeight: '2.75rem' }],
        '5xl': ['3.125rem', { lineHeight: '1.125' }],
        '6xl': ['3.875rem', { lineHeight: '1.125' }],
        '7xl': ['4.625rem', { lineHeight: '1.125' }],
        '8xl': ['6.125rem', { lineHeight: '1.125' }],
        '9xl': ['8.125rem', { lineHeight: '1.125' }],
      },
      colors:{
        "littlegreen":"#31C7A2",
        'lightyellow':"#FAF9F0",
        'lightgrey':"#F4F4F4",
        'darkyellow':"#FBBA15",
        'prograsblue':'#14BEFD',
        'weightgrey':'#3C3D3F',
      },
      //  screens: {
      //   '11inch': { 'max': '1366px' }, // 11-inch MacBook Air
      //   '13inch-air': { 'max': '1440px' }, // 13-inch MacBook Air
      //   '13inch-pro': { 'max': '1280px' }, // 13-inch MacBook Pro
      //   'desktop-1024': { 'max': '1024px' }, // Desktop 1024 x 768
      //   'desktop-1280x1024': { 'max': '1280px' }, // Desktop 1280 x 1024
      //   'desktop-1280x720': { 'max': '1280px' }, // Desktop 1280 x 720
      //   'desktop-1280x800': { 'max': '1280px' }, // Desktop 1280 x 800
      //   'desktop-1366x768': { 'max': '1366px' }, // Desktop 1366 x 768
      // },
    },
  },
  plugins: [],
}

