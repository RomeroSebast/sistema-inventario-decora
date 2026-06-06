<template>
  <div :class="{ 'layout-container': auth.isAuthenticated }">
    
    <!-- BARRA LATERAL (Solo aparece si el usuario inició sesión) -->
    <aside v-if="auth.isAuthenticated" class="sidebar">
      <div class="brand">
        <h2>DECORA</h2>
        <span>SHOWROOM</span>
      </div>

      <nav class="menu">
        <router-link to="/dashboard" class="nav-button">
          <i class="fas fa-boxes"></i> INVENTARIO
        </router-link>

        <router-link to="/historial" class="nav-button">
          <i class="fas fa-history"></i> HISTORIAL
        </router-link>

        <router-link v-if="auth.user?.tipo_usuario === 'Admin' || auth.user?.tipo_usuario === 'UsuarioComun'" to="/usuarios" class="nav-button">
          <i class="fas fa-users-cog"></i> USUARIOS
        </router-link>
      </nav>

      <div class="footer-sidebar">
        <button @click="handleLogout" class="btn-logout">
          <i class="fas fa-sign-out-alt"></i> SALIR
        </button>
      </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <!-- Si no está logueado, aplica 'login-wrapper-full' para un fondo oscuro unificado y elegante -->
    <main :class="{ 'main-content': auth.isAuthenticated, 'login-wrapper-full': !auth.isAuthenticated }">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const handleLogout = () => {
  auth.logout()
  router.push('/')
}
</script>

<style>
/* ==========================================================================
   ESTILOS GENERALES Y SIDEBAR ORIGINAL (DISEÑO OSCURO ELEGANTE)
   ========================================================================== */
body { 
  margin: 0; 
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
  background: #121212; 
  color: #f5f5f5;
}

.layout-container { 
  display: flex; 
}

/* DISEÑO DE LA SIDEBAR DECORA */
.sidebar {
  width: 260px;
  height: 100vh;
  background: #1a1a1a;
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  border-right: 1px solid #2d2d2d;
  z-index: 100;
}

.brand { padding: 30px 20px; text-align: center; }
.brand h2 { margin: 0; color: #28a745; letter-spacing: 2px; font-weight: bold; }
.brand span { font-size: 0.75rem; color: #777; letter-spacing: 1px; }

.menu {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* BOTONES ORIGINALES CON HOVER */
.nav-button {
  text-decoration: none;
  color: #eee;
  background: #2a2a2a;
  padding: 14px;
  border-radius: 8px;
  font-weight: bold;
  font-size: 0.9rem;
  transition: 0.3s ease;
  display: flex;
  align-items: center;
  gap: 10px;
  border-left: 4px solid transparent;
}

.nav-button:hover, .router-link-active {
  background: #28a745;
  color: white;
  border-left: 4px solid #218838;
}

.footer-sidebar {
  margin-top: auto;
  padding: 20px;
  border-top: 1px solid #2d2d2d;
}

.btn-logout {
  width: 100%;
  padding: 12px;
  background: #dc3545;
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 8px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background 0.2s;
}
.btn-logout:hover {
  background: #c0392b;
}

/* COMPORTAMIENTO DE LAS VISTAS INTERNAS */
.main-content {
  flex: 1;
  margin-left: 260px; 
  padding: 40px;
  box-sizing: border-box;
}

/* ==========================================================================
   NUEVO REDISEÑO PREMIUM PARA EL LOGIN DECORA SHOWROOM
   ========================================================================== */
.login-wrapper-full {
  width: 100vw;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: radial-gradient(circle, #1e1e1e 0%, #111111 100%); /* Fondo degradado sofisticado */
}

/* Forzar que la tarjeta del Login se vea oscura y combine con el resto */
.login-wrapper-full .card, 
.login-wrapper-full div[style*="background-color: white"],
.login-wrapper-full div[style*="background: white"] {
  background: #1e1e1e !important;
  border: 1px solid #2d2d2d !important;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5) !important;
  border-radius: 16px !important;
  padding: 35px !important;
  color: #f5f5f5 !important;
}

/* Corregir el título para que tenga contraste excelente */
.login-wrapper-full h1, 
.login-wrapper-full h2, 
.login-wrapper-full .title {
  color: #ffffff !important;
  font-weight: bold !important;
  letter-spacing: 1px !important;
  margin-bottom: 25px !important;
  text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

/* Ajustes para etiquetas e inputs dentro del login */
.login-wrapper-full label {
  color: #aaa !important;
  font-size: 0.85rem !important;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.login-wrapper-full input {
  background: #2a2a2a !important;
  border: 1px solid #3d3d3d !important;
  color: #ffffff !important;
  border-radius: 8px !important;
  padding: 12px !important;
}

.login-wrapper-full input:focus {
  border-color: #28a745 !important;
  outline: none !important;
}
</style>