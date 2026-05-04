<template>
  <div class="login-page">
    <div class="card">
      <h1>SISTEMA DE INVENTARIO</h1>
      <form @submit.prevent="handleLogin">
        <div class="input-group">
          <label>USUARIO</label>
          <input v-model="form.usuario" type="text" required placeholder="Ej: val_admin">
        </div>
        <div class="input-group">
          <label>CONTRASEÑA</label>
          <input v-model="form.contraseña" type="password" required placeholder="********">
        </div>
        <button type="submit" :disabled="loading">
          {{ loading ? 'CARGANDO...' : 'ENTRAR' }}
        </button>
      </form>
      <!-- Mensaje de error elegante en lugar de alertas -->
      <transition name="fade">
        <p v-if="error" class="error-msg">{{ error }}</p>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const form = ref({ usuario: '', contraseña: '' })
const error = ref('')
const loading = ref(false)
const auth = useAuthStore()
const router = useRouter()

const handleLogin = async () => {
  error.value = ''
  loading.value = true
  
  try {
    // Usamos la ruta completa ya validada en htdocs
    const url = 'http://localhost/sistema-inventario/backend/login.php'
    const res = await axios.post(url, form.value)
    
    if (res.data.status === 'success') {
      auth.login(res.data.usuario)
      router.push('/dashboard')
    } else {
      error.value = res.data.message || "DATOS INCORRECTOS"
    }
  } catch (err) {
    console.error("Error de conexión:", err)
    error.value = "ERROR DE RED: VERIFICA TU CONEXIÓN AL SERVIDOR"
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page { 
  height: 100vh; 
  display: flex; 
  justify-content: center; 
  align-items: center; 
  background: #222; 
}
.card { 
  background: white; 
  padding: 40px; 
  border-radius: 12px; 
  width: 380px; 
  text-align: center; 
  box-shadow: 0 10px 25px rgba(0,0,0,0.5);
}
.input-group { 
  text-align: left; 
  margin-bottom: 15px; 
}
label { 
  font-weight: bold; 
  font-size: 0.8rem; 
  color: #555; 
}
input { 
  width: 100%; 
  padding: 12px; 
  margin-top: 5px; 
  border: 1px solid #ddd; 
  border-radius: 6px; 
  box-sizing: border-box; 
}
button { 
  width: 100%; 
  background: #28a745; 
  color: white; 
  border: none; 
  padding: 14px; 
  cursor: pointer; 
  border-radius: 6px; 
  font-weight: bold;
  transition: 0.3s;
}
button:hover { background: #218838; }
button:disabled { background: #ccc; cursor: not-allowed; }

.error-msg { 
  color: #dc3545; 
  margin-top: 15px; 
  font-size: 0.9rem; 
  font-weight: bold;
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>