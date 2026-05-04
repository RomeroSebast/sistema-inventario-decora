<template>
  <div class="sidebar">
    <div class="brand">
      <h2>DECORA</h2>
      <span>INVENTARIO</span>
    </div>

    <nav class="menu">
      <!-- Botón Inventario -->
      <router-link to="/dashboard" class="nav-button">
        <i class="fas fa-boxes"></i> INVENTARIO
      </router-link>

      <!-- Botón Historial -->
      <router-link to="/historial" class="nav-button">
        <i class="fas fa-history"></i> HISTORIAL
      </router-link>
      
      <!-- Botón Usuarios (Solo Admin) -->
      <router-link v-if="auth.user?.tipo_usuario === 'Admin'" to="/usuarios" class="nav-button admin-style">
        <i class="fas fa-users-cog"></i> USUARIOS
      </router-link>
    </nav>
    
    <div class="footer-sidebar">
      <p class="user-name">{{ auth.user?.nombre }}</p>
      <button @click="handleLogout" class="btn-logout">
        <i class="fas fa-sign-out-alt"></i> SALIR
      </button>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

const handleLogout = () => {
  auth.logout();
  router.push('/');
};
</script>

<style scoped>
/* Contenedor principal de la barra lateral */
.sidebar {
  width: 260px;
  height: 100vh;
  background: #1a1a1a; /* Fondo oscuro elegante */
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  box-shadow: 4px 0 10px rgba(0,0,0,0.3);
}

.brand {
  padding: 30px 20px;
  text-align: center;
  border-bottom: 1px solid #333;
}

.brand h2 {
  margin: 0;
  color: #28a745; /* Verde Decora */
  letter-spacing: 3px;
  font-size: 1.5rem;
}

/* Contenedor del Menú */
.menu {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  padding: 20px 15px;
  gap: 12px; /* Esto separa los botones automáticamente */
}

/* Estilo de los Enlaces como BOTONES */
.nav-button {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #bdc3c7;
  text-decoration: none;
  padding: 12px 20px;
  background: #2c3e50; /* Color base del botón */
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  border-left: 4px solid transparent;
}

/* Efecto cuando pasas el mouse o el botón está activo */
.nav-button:hover, .router-link-active {
  background: #34495e;
  color: white;
  border-left: 4px solid #28a745;
  transform: translateX(5px); /* Se mueve un poquito a la derecha */
}

.admin-style {
  background: #34495e;
  color: #f1c40f; /* Color dorado para admin */
}

/* Footer y Botón Salir */
.footer-sidebar {
  padding: 20px;
  background: #111;
  text-align: center;
}

.user-name {
  font-size: 0.8rem;
  color: #888;
  margin-bottom: 10px;
  text-transform: uppercase;
}

.btn-logout {
  background: #e74c3c;
  color: white;
  border: none;
  padding: 10px;
  cursor: pointer;
  width: 100%;
  border-radius: 6px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background 0.3s;
}

.btn-logout:hover {
  background: #c0392b;
}
</style>