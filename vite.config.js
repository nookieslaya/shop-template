import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
  root: '.',
  base: '/wp-content/themes/shop-theme/public/build/',
  build: {
    outDir: 'public/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, 'resources/js/app.js'),
      },
      output: {
        entryFileNames: '[name].js',
        chunkFileNames: 'chunks/[name]-[hash].js',
        assetFileNames: ({ name }) => {
          if (name && name.endsWith('.css')) {
            return 'main.css';
          }
          return 'assets/[name]-[hash][extname]';
        },
      },
    },
  },
});
