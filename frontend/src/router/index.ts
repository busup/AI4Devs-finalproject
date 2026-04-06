import { createRouter, createWebHistory } from 'vue-router'

import BlankOutlet from '@/components/system/BlankOutlet.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'root',
      component: BlankOutlet,
    },
  ],
})

export default router
