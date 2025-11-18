import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  server: {
    host: '0.0.0.0',   // listen on all interfaces *inside* the container
    port: 5173,        // optional; matches your docker-compose command
    hmr: {
      host: 'localhost', // what the BROWSER uses to reach Vite
      port: 5173,
    },
  },
  plugins: [
    laravel({
      input: 'resources/js/app.js',
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
});
