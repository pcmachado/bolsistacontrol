import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    // Carrega as variáveis de ambiente (incluindo as passadas pelo cross-env)
    const env = loadEnv(mode, process.cwd(), '');

    return {
        // Define o base path dinamicamente. Se não existir, usa a raiz '/'
        base: env.VITE_BASE_URL || '/',

        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],
                refresh: true,
            }),
        ],
    };
});
