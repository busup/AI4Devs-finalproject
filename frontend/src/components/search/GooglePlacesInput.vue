<script setup lang="ts">
import { onMounted, ref } from 'vue'

import { hasGoogleMapsApiKey, loadGoogleMaps } from '@/lib/googleMapsLoader'

defineProps<{
  modelValue: string
  inputId?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
  /** Coordenadas del lugar elegido en autocomplete; `null` si el usuario edita el texto o no hay geometry. */
  'coords-change': [coords: { lat: number; lng: number } | null]
}>()

const inputRef = ref<HTMLInputElement | null>(null)
/** Evita borrar coords en el `input` que dispara Google al rellenar la dirección elegida. */
let skipCoordsClearFromInput = false

onMounted(async () => {
  if (!hasGoogleMapsApiKey() || !inputRef.value) return
  try {
    await loadGoogleMaps()
    if (!inputRef.value) return
    const ac = new google.maps.places.Autocomplete(inputRef.value, {
      types: ['address'],
      fields: ['formatted_address', 'geometry', 'name'],
    })
    ac.addListener('place_changed', () => {
      const place = ac.getPlace()
      const addr = place.formatted_address ?? inputRef.value?.value ?? ''
      skipCoordsClearFromInput = true
      emit('update:modelValue', addr)
      const loc = place.geometry?.location
      if (loc) {
        emit('coords-change', { lat: loc.lat(), lng: loc.lng() })
      } else {
        emit('coords-change', null)
      }
      setTimeout(() => {
        skipCoordsClearFromInput = false
      }, 0)
    })
  } catch (e) {
    console.warn('[GooglePlacesInput]', e)
  }
})
</script>

<template>
  <input
    :id="inputId"
    ref="inputRef"
    type="text"
    autocomplete="off"
    :value="modelValue"
    class="field-location box-border h-12 w-full rounded-lg border border-neutral-200 bg-white px-4 text-left text-base leading-normal text-neutral-700 outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500 dark:focus:border-emerald-500 dark:focus:ring-emerald-600"
    :placeholder="
      hasGoogleMapsApiKey() ? 'Buscar dirección…' : 'Configure VITE_GOOGLE_MAPS_API_KEY en .env'
    "
    :disabled="!hasGoogleMapsApiKey()"
    @input="
      ($event: Event) => {
        if (!skipCoordsClearFromInput) {
          emit('coords-change', null)
        }
        emit('update:modelValue', ($event.target as HTMLInputElement).value)
      }
    "
  />
</template>
