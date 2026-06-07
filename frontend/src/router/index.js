import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import HistorialView from '../views/HistorialView.vue'
import UsuariosView from '../views/UsuariosView.vue'
import ProveedoresView from '../views/ProveedoresView.vue' 

const routes = [
  {
    path: '/',
    name: 'login',
    component: LoginView
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardView,
    meta: { requiresAuth: true }
  },
  {
    path: '/historial',
    name: 'historial',
    component: HistorialView,
    meta: { requiresAuth: true }
  },
  {
    path: '/usuarios',
    name: 'usuarios',
    component: UsuariosView,
    meta: { requiresAuth: true }
  },
  {
    path: '/proveedores',
    name: 'proveedores',
    component: ProveedoresView,
    meta: { requiresAuth: true } // Protegida para usuarios con sesión activa
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// Guardián de seguridad: evita que entren a las rutas si no han iniciado sesión
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router