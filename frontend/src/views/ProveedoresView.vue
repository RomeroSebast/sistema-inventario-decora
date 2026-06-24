<template>
  <div class="main-container">
    <header class="header">
      <h1><i class="fas fa-truck-loading"></i> GESTIÓN DE PROVEEDORES</h1>
    </header>

    <section v-if="auth.user?.tipo_usuario === 'Admin'" class="card admin-border">
      <h3>REGISTRAR NUEVO PROVEEDOR</h3>
      <div class="form-grid">
        <div class="input-field">
          <input v-model="nuevoProv.nombre" placeholder="NOMBRE DE LA EMPRESA / DISTRIBUIDORA">
        </div>
        <div class="input-field">
          <input v-model="nuevoProv.contacto" placeholder="CORREO / TELÉFONO DE CONTACTO">
        </div>
        <button @click="crearProveedor" class="btn-save">AGREGAR PROVEEDOR</button>
      </div>
    </section>

    <section class="card">
      <div class="table-header-flex">
        <h3>Directorio de Proveedores</h3>
        <div class="search-box">
          <i class="fas fa-search search-icon"></i>
          <input v-model="buscarTermino" placeholder="Buscar por nombre o contacto..." class="input-search">
        </div>
      </div>

      <table class="user-table">
        <thead>
          <tr>
            <th>ID INTERNO</th>
            <th>PROVEEDOR / DISTRIBUIDOR</th>
            <th>DATOS DE CONTACTO</th>
            <th v-if="auth.user?.tipo_usuario === 'Admin'">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in proveedoresFiltrados" :key="p.id_proveedor">
            <td class="id-text">#{{ p.id_proveedor }}</td>
            <td class="text-white font-bold">{{ p.nombre }}</td>
            <td class="text-muted">{{ p.contacto }}</td>
            <td v-if="auth.user?.tipo_usuario === 'Admin'">
              <button @click="borrarProveedor(p.id_proveedor)" class="btn-delete">
                <i class="fas fa-trash-alt"></i> ELIMINAR
              </button>
            </td>
          </tr>
          <tr v-if="proveedoresFiltrados.length === 0">
            <td colspan="4" class="text-center text-muted">No se encontraron proveedores que coincidan.</td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth'; // Importamos el validador de sesión

const auth = useAuthStore(); // Instanciamos la sesión activa
const proveedores = ref([]);
const buscarTermino = ref('');
const nuevoProv = ref({ nombre: '', contacto: '' });
const url = 'http://localhost/sistema-inventario/backend/proveedores.php';

const cargarProveedores = async () => {
  try {
    const res = await axios.get(url);
    proveedores.value = res.data;
  } catch (err) {
    console.error("Error al cargar proveedores:", err);
  }
};

const crearProveedor = async () => {
  if (auth.user?.tipo_usuario !== 'Admin') return alert("No tienes permisos.");
  if (!nuevoProv.value.nombre) return alert("EL NOMBRE DEL PROVEEDOR ES OBLIGATORIO");
  
  try {
    const res = await axios.post(url, nuevoProv.value);
    if(res.data.status === 'success' || !res.data.status) {
      alert("¡Proveedor añadido con éxito!");
      nuevoProv.value = { nombre: '', contacto: '' };
      await cargarProveedores();
    }
  } catch (err) {
    alert("Error al guardar en el servidor");
  }
};

const borrarProveedor = async (id) => {
  if (auth.user?.tipo_usuario !== 'Admin') return alert("No tienes permisos.");
  if (confirm("¿ESTÁS SEGURO DE ELIMINAR ESTE PROVEEDOR?")) {
    try {
      const res = await axios.delete(`${url}?id=${id}`);
      if(res.data.status === 'success' || !res.data.status) {
        await cargarProveedores();
      }
    } catch (err) {
      alert("Error al eliminar el registro");
    }
  }
};

const proveedoresFiltrados = computed(() => {
  if (!buscarTermino.value) return proveedores.value;
  const termino = buscarTermino.value.toLowerCase().trim();
  return proveedores.value.filter(p => {
    const nombre = (p.nombre || '').toLowerCase();
    const contacto = (p.contacto || '').toLowerCase();
    return nombre.includes(termino) || contacto.includes(termino);
  });
});

onMounted(cargarProveedores);
</script>

<style scoped>
.main-container { padding: 30px; max-width: 1200px; min-height: 100vh; background: #121212; color: #f5f5f5; }
.header h1 { color: #28a745; font-size: 1.8rem; margin-bottom: 25px; letter-spacing: 1px; font-weight: bold; }

.card { background: #1e1e1e; padding: 25px; border-radius: 14px; margin-bottom: 25px; border: 1px solid #2d2d2d; box-shadow: 0 8px 30px rgba(0,0,0,0.2); }
.admin-border { border-top: 4px solid #28a745; }
.card h3 { color: #aaa; font-size: 1rem; margin-top: 0; margin-bottom: 20px; letter-spacing: 1px; }

.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 15px; align-items: center; }

input { width: 100%; padding: 12px; background: #2a2a2a; border: 1px solid #3d3d3d; color: #ffffff !important; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box; }
input::placeholder { color: #666; }
input:focus { border-color: #28a745; outline: none; }

.btn-save { background: #28a745; color: white; border: none; padding: 12px 20px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: background 0.3s; width: 100%; }
.btn-save:hover { background: #218838; }

.table-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 15px; }
.search-box { position: relative; display: flex; align-items: center; width: 320px; }
.search-icon { position: absolute; left: 12px; color: #777; }
.input-search { width: 100%; padding: 10px 10px 10px 38px !important; background: #2a2a2a; border: 1px solid #3d3d3d; color: white; border-radius: 8px; font-size: 0.85rem; }

.user-table { width: 100%; border-collapse: collapse; }
.user-table th { text-align: left; padding: 16px; border-bottom: 2px solid #333; color: #28a745; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
.user-table td { padding: 16px; border-bottom: 1px solid #252525; vertical-align: middle; }

.text-white { color: #ffffff; }
.text-muted { color: #aaa; }
.font-bold { font-weight: 600; }
.id-text { font-family: monospace; color: #ffc107; font-weight: bold; }

.btn-delete { background: #dc3545; color: white; border: none; padding: 8px 12px; cursor: pointer; border-radius: 6px; font-weight: bold; font-size: 0.8rem; transition: 0.2s; }
.btn-delete:hover { background: #bd2130; }
.text-center { text-align: center !important; padding: 25px !important; }
</style>