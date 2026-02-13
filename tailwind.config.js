/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
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

