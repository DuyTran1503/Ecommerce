/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "node_modules/preline/dist/*.js",
        "./app/Http/Livewire/**/*.php",
    ],
    darkMode: "class",
    theme: {
        extend: {},
    },
    plugins: [require("preline/plugin")],
};
