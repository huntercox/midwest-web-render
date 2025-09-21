import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  server: {
    server: {
      host: '0.0.0.0', // or use 'true' for same effect
      port: 5173 // your desired port
    }
  },
  plugins: [
    laravel({
      input: ['resources/scss/app.scss', 'resources/js/app.js'],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  resolve: {
    alias: {
      '@': '/resources/js',
    },
  },
  css: {
    devSourcemap: true,
  },
});
