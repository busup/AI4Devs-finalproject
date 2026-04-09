import { ref } from 'vue'

const STORAGE_KEY = 'route-searcher-theme'

function readDarkFromDom(): boolean {
  if (typeof document === 'undefined') {
    return false
  }
  return document.documentElement.classList.contains('dark')
}

/** `true` si `<html>` tiene clase `dark` (modo noche). */
export const isDark = ref(readDarkFromDom())

export function syncThemeFromDocument(): void {
  if (typeof document === 'undefined') {
    return
  }
  isDark.value = document.documentElement.classList.contains('dark')
}

/** Alterna modo claro / oscuro y persiste en `localStorage`. */
export function toggleColorMode(): void {
  const next = !isDark.value
  isDark.value = next
  document.documentElement.classList.toggle('dark', next)
  try {
    localStorage.setItem(STORAGE_KEY, next ? 'dark' : 'light')
  } catch {
    /* modo privado u otro bloqueo */
  }
}

export function useTheme() {
  return { isDark, toggleColorMode, syncThemeFromDocument }
}
