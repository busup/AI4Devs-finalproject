import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import SearchView from '@/views/SearchView.vue'
import ReviewBookingView from '@/views/ReviewBookingView.vue'
import PaymentView from '@/views/PaymentView.vue'
import ConfirmationView from '@/views/ConfirmationView.vue'
import HomeView from '@/views/HomeView.vue'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'search',
    component: SearchView,
    meta: { title: 'Buscar rutas | Route Searcher' },
  },
  {
    path: '/busqueda',
    redirect: '/',
  },
  {
    path: '/home',
    name: 'home',
    component: HomeView,
    meta: { title: 'Route Searcher' },
  },
  {
    path: '/review',
    name: 'review',
    component: ReviewBookingView,
    meta: { title: 'Revisar reserva | Route Searcher' },
  },
  {
    path: '/pago',
    name: 'payment',
    component: PaymentView,
    meta: { title: 'Pago | Route Searcher' },
  },
  {
    path: '/confirmacion',
    name: 'confirmation',
    component: ConfirmationView,
    meta: { title: 'Confirmación | Route Searcher' },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.afterEach((to) => {
  const title = to.meta?.title as string | undefined
  if (title) document.title = title
})

export default router
