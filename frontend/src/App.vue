<script setup lang="ts">
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
  <div class="rs-app min-h-screen p-6 text-neutral-800">
    <p class="text-lg font-medium">{{ t('app.placeholder') }}</p>
    <router-view />
  </div>
</template>
