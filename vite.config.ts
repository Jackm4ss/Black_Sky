import { defineConfig } from 'vite'
import path from 'path'
import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react'
import laravel from 'laravel-vite-plugin'
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer'


export default defineConfig({
  plugins: [
    laravel({
      input: ['src/main.tsx', 'src/filament-auth-shape-grid.tsx', 'src/filament-admin.ts'],
      refresh: ['app/**', 'config/**', 'resources/views/**', 'routes/**', 'src/**'],
    }),
    // The React and Tailwind plugins are both required for Make, even if
    // Tailwind is not being actively used – do not remove them
    react(),
    tailwindcss(),
    ViteImageOptimizer({
      logStats: true,
      ansiColors: true,
      includePublic: true,
      cache: true,
      cacheLocation: path.resolve(__dirname, 'node_modules/.cache/vite-image-optimizer'),
      png: {
        quality: 92,
      },
      jpeg: {
        quality: 86,
        mozjpeg: true,
      },
      jpg: {
        quality: 86,
        mozjpeg: true,
      },
      webp: {
        quality: 86,
      },
      avif: {
        quality: 72,
      },
      svg: {
        multipass: true,
        plugins: [
          {
            name: 'preset-default',
            params: {
              overrides: {
                removeViewBox: false,
                cleanupIds: {
                  minify: false,
                  remove: false,
                },
              },
            },
          },
          'sortAttrs',
        ],
      },
    }),
  ],
  resolve: {
    alias: {
      // Alias @ to the src directory
      '@': path.resolve(__dirname, './src'),
    },
  },

  server: {
    host: '127.0.0.1',
    hmr: {
      host: '127.0.0.1',
    },
  },

  build: {
    rollupOptions: {
      output: {
        manualChunks(id) {
          const normalizedId = id.replace(/\\/g, '/')

          if (!normalizedId.includes('/node_modules/')) {
            return undefined
          }

          if (
            normalizedId.includes('/react/') ||
            normalizedId.includes('/react-dom/') ||
            normalizedId.includes('/scheduler/')
          ) {
            return 'react-vendor'
          }

          if (normalizedId.includes('/react-router/')) {
            return 'router-vendor'
          }

          if (
            normalizedId.includes('/@tanstack/') ||
            normalizedId.includes('/axios/')
          ) {
            return 'api-vendor'
          }

          if (
            normalizedId.includes('/react-hook-form/') ||
            normalizedId.includes('/@hookform/') ||
            normalizedId.includes('/zod/')
          ) {
            return 'form-vendor'
          }

          if (
            normalizedId.includes('/@radix-ui/') ||
            normalizedId.includes('/lucide-react/') ||
            normalizedId.includes('/class-variance-authority/') ||
            normalizedId.includes('/clsx/') ||
            normalizedId.includes('/tailwind-merge/')
          ) {
            return 'ui-vendor'
          }

          return undefined
        },
      },
    },
  },

  // File types to support raw imports. Never add .css, .tsx, or .ts files to this.
  assetsInclude: ['**/*.svg', '**/*.csv'],
})
