<script setup lang="ts">
import { onMounted } from 'vue'

import { syncThemeFromDocument } from '@/composables/useTheme'

onMounted(() => {
  syncThemeFromDocument()
})
</script>

<template>
  <div
    class="rs-app min-h-screen bg-neutral-100 text-neutral-800 transition-colors duration-200 dark:bg-neutral-950 dark:text-neutral-100"
  >
    <router-view />
  </div>
</template>
