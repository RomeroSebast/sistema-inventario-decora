<template>
  <div class="main-container">
    <header class="header">
      <h1><i class="fas fa-users-cog"></i> GESTIÓN DE PERSONAL</h1>
    </header>

    <!-- Formulario de Registro -->
    <section class="card admin-border">
      <h3>REGISTRAR NUEVO EMPLEADO</h3>
      <div class="form-grid">
        <div class="input-field">
          <input v-model="nuevo.nombre" placeholder="NOMBRE COMPLETO">
        </div>
        <div class="input-field">
          <input v-model="nuevo.usuario" placeholder="USUARIO DE ACCESO">
        </div>
        <div class="input-field">
          <input v-model="nuevo.contraseña" type="password" placeholder="CONTRASEÑA">
        </div>
        <div class="input-field">
          <select v-model="nuevo.tipo_usuario">
            <option value="UsuarioComun">USUARIO COMÚN</option>
            <option value="Admin">ADMINISTRADOR</option>
          </select>
        </div>
        <button @click="crearUsuario" class="btn-save">CREAR CUENTA</button>
      </div>
    </section>

    <!-- Tabla de Personal -->
    <section class="card">
      <table class="user-table">
        <thead>
          <tr>
            <th>NOMBRE</th>
            <th>USUARIO</th>
            <th>ROL</th>
            <th>ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in usuarios" :key="u.id_usuario">
            <td class="text-white font-bold">{{ u.nombre }}</td>
            <td class="text-muted">{{ u.usuario }}</td>
            <td>
              <span :class="['role-badge', u.tipo_usuario === 'Admin' ? 'badge-admin' : 'badge-user']">
                {{ u.tipo_usuario }}
              </span>
            </td>
            <td>
              <button @click="borrar(u.id_usuario)" class="btn-delete" v-if="u.usuario !== 'val_admin'">
                <i class="fas fa-trash-alt"></i> ELIMINAR
              </button>
              <span v-else class="text-protected"><i class="fas fa-lock"></i> PROTEGIDO</span>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const usuarios = ref([]);
const nuevo = ref({ nombre: '', usuario: '', contraseña: '', tipo_usuario: 'UsuarioComun' });
const url = 'http://localhost/sistema-inventario/backend/usuarios.php';

const cargar = async () => {
  try {
    const res = await axios.get(url);
    usuarios.value = res.data;
  } catch (err) {
    console.error("Error al cargar usuarios:", err);
  }
};

const crearUsuario = async () => {
  if (!nuevo.value.nombre || !nuevo.value.usuario || !nuevo.value.contraseña) {
    return alert("POR FAVOR, LLENA TODOS LOS CAMPOS");
  }
  const res = await axios.post(url, nuevo.value);
  if(res.data.status === 'success' || !res.data.status) {
    nuevo.value = { nombre: '', usuario: '', contraseña: '', tipo_usuario: 'UsuarioComun' };
    cargar();
  }
};

const borrar = async (id) => {
  if (confirm("¿ESTÁS SEGURO DE ELIMINAR ESTE USUARIO?")) {
    await axios.delete(`${url}?id=${id}`);
    cargar();
  }
};

onMounted(cargar);
</script>

<style scoped>
.main-container { padding: 30px; max-width: 1200px; min-height: 100vh; background: #121212; color: #f5f5f5; }
.header h1 { color: #f5f5f5; font-size: 1.8rem; margin-bottom: 25px; letter-spacing: 1px; font-weight: bold; }

.card { background: #1e1e1e; padding: 25px; border-radius: 14px; margin-bottom: 25px; border: 1px solid #2d2d2d; box-shadow: 0 8px 30px rgba(0,0,0,0.2); }
.admin-border { border-top: 4px solid #007bff; }
.card h3 { color: #aaa; font-size: 1rem; margin-top: 0; margin-bottom: 20px; letter-spacing: 1px; }

.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: center; }

input, select { width: 100%; padding: 12px; background: #2a2a2a; border: 1px solid #3d3d3d; color: #ffffff !important; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box; }
input::placeholder { color: #777; }
input:focus, select:focus { border-color: #007bff; outline: none; }

.btn-save { background: #007bff; color: white; border: none; padding: 12px 20px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: background 0.3s; width: 100%; height: 100%; }
.btn-save:hover { background: #0056b3; }

/* TABLA CON TEXTOS CORREGIDOS */
.user-table { width: 100%; border-collapse: collapse; }
.user-table th { text-align: left; padding: 16px; border-bottom: 2px solid #333; color: #007bff; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
.user-table td { padding: 16px; border-bottom: 1px solid #252525; vertical-align: middle; }

.text-white { color: #ffffff; }
.text-muted { color: #aaa; }
.font-bold { font-weight: 600; }

.role-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
.badge-admin { background: rgba(255, 193, 7, 0.15); color: #ffc107; border: 1px solid #ffc107; }
.badge-user { background: rgba(17, 205, 239, 0.15); color: #11cdef; border: 1px solid #11cdef; }

.btn-delete { background: #dc3545; color: white; border: none; padding: 8px 12px; cursor: pointer; border-radius: 6px; font-weight: bold; font-size: 0.8rem; transition: 0.2s; }
.btn-delete:hover { background: #bd2130; }
.text-protected { color: #666; font-size: 0.85rem; font-style: italic; }
</style>