import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: ['./src/test/setup.ts'],
    include: ['src/**/*.{test,spec}.{js,ts,jsx,tsx}'],
    exclude: ['node_modules/', 'dist/', '.storybook/'],

    // Performance optimizations
    pool: 'forks',
    poolOptions: {
      forks: {
        singleFork: true,
      },
    },

    // Test timeout
    testTimeout: 10000,
    hookTimeout: 10000,

    // Watch mode settings
    watch: {
      ignore: ['**/node_modules/**', '**/dist/**'],
    },

    // Coverage configuration
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html', 'lcov'],
      exclude: [
        'node_modules/',
        'src/test/',
        'src/vite-env.d.ts',
        '**/*.stories.{js,ts,jsx,tsx}',
        '**/*.test.{js,ts,jsx,tsx}',
        '**/*.spec.{js,ts,jsx,tsx}',
        '**/*.config.{js,ts}',
        '**/coverage/**',
        '**/dist/**',
        '**/.storybook/**',
      ],
      include: ['src/**/*.{js,ts,jsx,tsx}'],
      thresholds: {
        global: {
          branches: 80,
          functions: 80,
          lines: 80,
          statements: 80,
        },
      },
      all: true,
      clean: true,
    },

    // Reporter configuration
    reporters: ['verbose', 'json'],
    outputFile: {
      json: './coverage/test-results.json',
    },
  },
});
