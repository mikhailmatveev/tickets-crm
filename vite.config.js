import {defineConfig, loadEnv} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({mode}) => {
  const env = loadEnv(mode, process.cwd(), '');
  const vitePort = Number(env.VITE_PORT) || 5173;

  return {
    plugins: [
      laravel({
        input: [
          'resources/sass/style.scss',
          'resources/js/app.js'
        ],
        refresh: true,
      }),
      vue(),
    ],
    server: {
      host: '0.0.0.0',
      port: vitePort,
      strictPort: true,
    },
  };
});
