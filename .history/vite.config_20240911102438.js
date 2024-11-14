import { defineConfig } from 'vite';
import laravel from 'vite-plugin-laravel';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

export default defineConfig({
    plugins: [
        laravel(),
    ],
    css: {
        postcss: {
            plugins: [
                tailwindcss({
                    // Tentukan file mana yang ingin diproses oleh Vite
                    input: ['resources/css/app.css', 'resources/js/app.js'],
                    refresh: true,
                }),
                autoprefixer(),
            ],
        },
    },
});
