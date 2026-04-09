<script setup lang="ts">
import { useI18n } from 'vue-i18n'

import BusLogoMark from '@/components/icons/BusLogoMark.vue'
import { useTheme } from '@/composables/useTheme'

const { t } = useI18n()
const { isDark, toggleColorMode } = useTheme()
</script>

<template>
  <header
    class="border-b border-neutral-200 bg-white transition-colors dark:border-neutral-800 dark:bg-neutral-900"
  >
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
      <div class="flex items-center gap-2">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500">
          <BusLogoMark />
        </div>
        <span class="text-xl font-semibold">
          <span class="text-emerald-500">Route</span>
          <span class="text-neutral-800 dark:text-neutral-100">Searcher</span>
        </span>
      </div>

      <nav class="hidden items-center gap-8 md:flex" aria-label="Principal">
        <a
          href="#"
          class="font-medium text-emerald-500 transition-colors hover:text-emerald-600 dark:text-emerald-400 dark:hover:text-emerald-300"
          @click.prevent
        >
          {{ t('nav.findRoutes') }}
        </a>
        <a
          href="#"
          class="text-neutral-600 transition-colors hover:text-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200"
          @click.prevent
        >
          {{ t('nav.myBookings') }}
        </a>
        <a
          href="#"
          class="text-neutral-600 transition-colors hover:text-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200"
          @click.prevent
        >
          {{ t('nav.companyPass') }}
        </a>
      </nav>

      <div class="flex items-center gap-4">
        <button
          type="button"
          class="rounded-lg p-2 text-neutral-600 transition-colors hover:bg-neutral-100 hover:text-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100"
          :aria-label="isDark ? t('nav.themeSwitchToLight') : t('nav.themeSwitchToDark')"
          @click="toggleColorMode"
        >
          <!-- Modo claro: luna → activar oscuro -->
          <svg
            v-if="!isDark"
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
          >
            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
          </svg>
          <!-- Modo oscuro: sol → volver a claro -->
          <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
          >
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2" />
            <path d="M12 20v2" />
            <path d="m4.93 4.93 1.41 1.41" />
            <path d="m17.66 17.66 1.41 1.41" />
            <path d="M2 12h2" />
            <path d="M20 12h2" />
            <path d="m6.34 17.66-1.41 1.41" />
            <path d="m19.07 4.93-1.41 1.41" />
          </svg>
        </button>
        <button
          type="button"
          class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-emerald-500 bg-emerald-50 text-emerald-600 transition-colors dark:border-emerald-400 dark:bg-emerald-950/50 dark:text-emerald-300"
          aria-label="Menú de usuario"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
          >
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
        </button>
      </div>
    </div>
  </header>
</template>
