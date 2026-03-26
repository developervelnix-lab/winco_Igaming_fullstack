/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
    "./node_modules/flowbite-react/**/*.js", // Add Flowbite React components
  ],
  theme: {
    extend: {
      colors: {
        brand: 'var(--brand)',
        'brand-light': 'var(--brand-light)',
        'brand-dark': 'var(--brand-dark)',
        bg: 'var(--bg)',
        secondaryBg: 'var(--secondaryBg)',
        bg2: 'var(--bg2)',
        bg3: 'var(--bg3)',
        bg4: 'var(--bg4)',
        text: 'var(--text)',
        muted: 'var(--muted)',
      }
    },
  },
  plugins: [
    require("flowbite/plugin"), // Use the Flowbite plugin
  ],
};
