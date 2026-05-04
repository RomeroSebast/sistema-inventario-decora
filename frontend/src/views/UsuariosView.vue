<template>
  <div class="container">
    <header class="view-header">
      <h1>GESTIÓN DE PERSONAL</h1>
    </header>

    <section class="card admin-border">
      <h3>REGISTRAR NUEVO EMPLEADO</h3>
      <div class="form-grid">
        <input v-model="nuevo.nombre" placeholder="NOMBRE COMPLETO">
        <input v-model="nuevo.usuario" placeholder="USUARIO DE ACCESO">
        <input v-model="nuevo.contraseña" type="password" placeholder="CONTRASEÑA">
        <select v-model="nuevo.tipo_usuario">
          <option value="UsuarioComun">USUARIO COMÚN</option>
          <option value="Admin">ADMINISTRADOR</option>
        </select>
        <button @click="crearUsuario" class="btn-save">CREAR CUENTA</button>
      </div>
    </section>

    <section class="card">
      <table class="main-table">
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
            <td>{{ u.nombre }}</td>
            <td>{{ u.usuario }}</td>
            <td><span class="role-badge">{{ u.tipo_usuario }}</span></td>
            <td>
              <button @click="borrar(u.id_usuario)" class="btn-delete" v-if="u.usuario !== 'val_admin'">ELIMINAR</button>
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
  const res = await axios.get(url);
  usuarios.value = res.data;
};

const crearUsuario = async () => {
  if (!nuevo.value.usuario || !nuevo.value.contraseña) return alert("LLENA LOS CAMPOS");
  await axios.post(url, nuevo.value);
  nuevo.value = { nombre: '', usuario: '', contraseña: '', tipo_usuario: 'UsuarioComun' };
  cargar();
};

const borrar = async (id) => {
  if (confirm("¿BORRAR ESTE USUARIO?")) {
    await axios.delete(`${url}?id=${id}`);
    cargar();
  }
};

onMounted(cargar);
</script>

<style scoped>
.admin-border { border-top: 4px solid #007bff; }
.role-badge { background: #e9ecef; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; }
.btn-save { background: #007bff; color: white; border: none; padding: 10px; cursor: pointer; border-radius: 4px; }
.btn-delete { background: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
/* ... reutiliza los estilos de DashboardView ... */
</style>