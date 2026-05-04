import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import UsuariosView from '../views/UsuariosView.vue'
import HistorialView from '../views/HistorialView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', component: LoginView },
    { path: '/dashboard', component: DashboardView, meta: { requiresAuth: true } },
    { path: '/usuarios', component: UsuariosView, meta: { requiresAuth: true } },
    { path: '/historial', component: HistorialView, meta: { requiresAuth: true } }
  ]
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isAuthenticated) next('/')
  else next()
})

export default router