<template>
  <div class="container">
    <header class="view-header">
      <h1>CONTROL DE INVENTARIO</h1>
    </header>

    <!-- FORMULARIO NUEVO PRODUCTO (SOLO ADMIN) -->
    <section v-if="auth.user?.tipo_usuario === 'Admin'" class="card admin-card">
      <h3><i class="fas fa-plus"></i> REGISTRAR NUEVO PRODUCTO</h3>
      <div class="form-grid">
        <input v-model="nuevoProd.nombre" placeholder="NOMBRE DEL PRODUCTO">
        <select v-model="nuevoProd.id_proveedor">
          <option value="" disabled>SELECCIONAR PROVEEDOR</option>
          <option v-for="prov in proveedores" :key="prov.id_proveedor" :value="prov.id_proveedor">
            {{ prov.nombre }}
          </option>
        </select>
        <button @click="crearProducto" class="btn-add">GUARDAR PRODUCTO</button>
      </div>
    </section>

    <!-- FORMULARIO MOVIMIENTOS (TODOS) -->
    <section class="card">
      <h3><i class="fas fa-exchange-alt"></i> REGISTRAR ENTRADA / SALIDA</h3>
      <div class="form-grid">
        <select v-model="formMov.id_producto">
          <option value="" disabled>SELECCIONAR PRODUCTO</option>
          <option v-for="p in productos" :key="p.id_producto" :value="p.id_producto">
            {{ p.nombre }}
          </option>
        </select>
        <input type="number" v-model="formMov.cantidad" placeholder="CANTIDAD">
        <select v-model="formMov.tipo">
          <option value="ENTRADA">ENTRADA</option>
          <option value="SALIDA">SALIDA</option>
        </select>
        <input v-if="formMov.tipo === 'SALIDA'" v-model="formMov.proyecto" placeholder="PROYECTO / DESTINO">
        <button @click="registrarMovimiento" class="btn-primary">REGISTRAR</button>
      </div>
    </section>

    <!-- TABLA DE STOCK -->
    <section class="card">
      <h3>ESTADO ACTUAL DEL ALMACÉN</h3>
      <table class="main-table">
        <thead>
          <tr>
            <th>PRODUCTO</th>
            <th>PROVEEDOR</th>
            <th>STOCK</th>
            <th v-if="auth.user?.tipo_usuario === 'Admin'">ACCIÓN</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in productos" :key="item.id_producto">
            <td>{{ item.nombre }}</td>
            <td>{{ item.proveedor }}</td>
            <td :class="{'low-stock': item.cantidad < 5}">{{ item.cantidad }}</td>
            <td v-if="auth.user?.tipo_usuario === 'Admin'">
              <button @click="eliminarProducto(item.id_producto)" class="btn-delete">BORRAR</button>
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
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const productos = ref([]);
const proveedores = ref([]);
const nuevoProd = ref({ nombre: '', id_proveedor: '' });
const formMov = ref({ id_producto: '', cantidad: 0, tipo: 'ENTRADA', proyecto: '', id_usuario: auth.user?.id_usuario });

const apiBase = 'http://localhost/sistema-inventario/backend';

const cargarDatos = async () => {
  const res = await axios.get(`${apiBase}/productos.php`);
  productos.value = res.data;
  
  // Cargamos proveedores (usando el endpoint de usuarios temporalmente o uno de proveedores)
  const resProv = await axios.get(`${apiBase}/usuarios.php`); 
  // Nota: Lo ideal es que crees proveedores.php, pero si usas el de usuarios, 
  // asegúrate que el backend devuelva proveedores reales.
  proveedores.value = [{id_proveedor: 1, nombre: 'PROVEEDOR GENERAL'}]; // Datos de prueba
};

const crearProducto = async () => {
  if(!nuevoProd.value.nombre || !nuevoProd.value.id_proveedor) return alert("COMPLETA LOS DATOS");
  const res = await axios.post(`${apiBase}/productos.php`, nuevoProd.value);
  if(res.data.status === 'success') {
    nuevoProd.value = { nombre: '', id_proveedor: '' };
    cargarDatos();
  }
};

const registrarMovimiento = async () => {
  if(formMov.value.cantidad <= 0) return alert("CANTIDAD INVÁLIDA");
  const res = await axios.post(`${apiBase}/movimientos.php`, formMov.value);
  if(res.data.status === 'success') {
    alert("STOCK ACTUALIZADO");
    formMov.value.cantidad = 0;
    cargarDatos();
  }
};

const eliminarProducto = async (id) => {
  if(confirm("¿ELIMINAR PRODUCTO?")) {
    await axios.delete(`${apiBase}/productos.php?id=${id}`);
    cargarDatos();
  }
};

onMounted(cargarDatos);
</script>

<style scoped>
.container { max-width: 1000px; margin: auto; }
.card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.admin-card { border-left: 5px solid #ffc107; }
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: end; }
input, select { padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
.btn-add { background: #ffc107; color: black; font-weight: bold; border: none; padding: 12px; cursor: pointer; }
.btn-primary { background: #28a745; color: white; border: none; padding: 12px; cursor: pointer; }
.btn-delete { background: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; }
.main-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.main-table th, .main-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
.low-stock { color: red; font-weight: bold; }
</style>