import { defineConfig } from 'vite';
import { resolve } from 'path';
import obfuscatorPlugin from 'vite-plugin-obfuscator';

export default defineConfig({
  plugins: [
    obfuscatorPlugin({
      globalOptions: {
        compact: true,
        controlFlowFlattening: true,
        deadCodeInjection: false,
        debugProtection: false,
        disableConsoleOutput: true,
        identifierNamesGenerator: 'hexadecimal',
        stringArray: true,
        stringArrayEncoding: ['base64'],
      }
    })
  ],
  build: {
    outDir: 'public/dist',
    emptyOutDir: false,
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'public/dist/js/app.js'),
        // Tambahkan entry point lain jika ada
      }
    }
  },
  server: {
    port: 5173,
    strictPort: true,
  }
});
