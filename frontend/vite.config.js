import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../public/build', // Output straight into the PHP public directory
    manifest: true,            // Generates a manifest.json to load from PHP
    rollupOptions: {
      input: './src/main.jsx',
    }
  }
});