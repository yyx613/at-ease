import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/product-list.js',
                'resources/scss/_base.scss',
                'resources/scss/_layout-front-end.scss',
                'resources/scss/_customer-list.scss',
                'resources/scss/_cart.scss',
                'resources/scss/_layout-admin.scss',
                'resources/scss/_home.scss',
                'resources/scss/_create-edit-user.scss',
                'resources/js/create-edit-user.js',
                'resources/js/home.js',
                'resources/scss/_login.scss',
                'resources/js/cart.js',
            ],
            refresh: true,
        }),
    ],
});
