<template>
  <div :class="{ 'layout-container': auth.isAuthenticated }">
    <!-- Solo se muestra si el usuario ya entró -->
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

        <router-link v-if="auth.user?.tipo_usuario === 'Admin'" to="/usuarios" class="nav-button admin-btn">
          <i class="fas fa-users-cog"></i> USUARIOS
        </router-link>
      </nav>

      <div class="footer-sidebar">
        <button @click="handleLogout" class="btn-logout">SALIR</button>
      </div>
    </aside>

    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'
const auth = useAuthStore()
const router = useRouter()
const handleLogout = () => { auth.logout(); router.push('/'); }
</script>

<style>
body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #121212; }

.layout-container { display: flex; }

/* BARRA LATERAL */
.sidebar {
  width: 260px;
  height: 100vh;
  background: #1a1a1a;
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  border-right: 1px solid #333;
}

.brand { padding: 30px 20px; text-align: center; }
.brand h2 { margin: 0; color: #28a745; letter-spacing: 2px; }

/* MENÚ DE BOTONES */
.menu {
  flex-grow: 1;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 15px; /* Esto separa los botones */
}

.nav-button {
  text-decoration: none;
  color: #eee;
  background: #2c2c2c;
  padding: 15px;
  border-radius: 8px;
  text-align: center;
  font-weight: bold;
  transition: 0.3s;
  display: block; /* Ocupa todo el ancho */
}

.nav-button:hover, .router-link-active {
  background: #28a745;
  color: white;
}

.admin-btn {
  border: 1px solid #ffc107;
  color: #ffc107;
}

.main-content {
  flex: 1;
  margin-left: 260px; /* Espacio para que no lo tape la sidebar */
  padding: 40px;
}

.btn-logout {
  width: 100%;
  padding: 10px;
  background: #dc3545;
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}
</style>