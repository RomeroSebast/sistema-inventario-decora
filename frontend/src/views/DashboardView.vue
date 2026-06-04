<template>
  <div class="main-container">
    <header class="header">
      <h1><i class="fas fa-layer-group"></i> Control de Almacén Decora</h1>
    </header>

    <!-- Formulario Nuevo Producto (Admin) con Clave de Proveedor -->
    <section v-if="auth.user?.tipo_usuario === 'Admin'" class="card admin-card">
      <h3><i class="fas fa-tag"></i> Registrar Producto con Clave</h3>
      <div class="form-grid">
        <input v-model="nuevoProd.nombre" placeholder="Nombre del Material">
        <input v-model="nuevoProd.clave_proveedor" placeholder="Clave de Proveedor (SKU)">
        <select v-model="nuevoProd.id_proveedor">
          <option value="" disabled>Seleccionar Proveedor</option>
          <option v-for="p in proveedores" :key="p.id_proveedor" :value="p.id_proveedor">{{ p.nombre }}</option>
        </select>
        <button @click="crearProducto" class="btn-admin">Guardar Código</button>
      </div>
    </section>

    <!-- Registro de Movimientos (Abiertos / Cerrados) -->
    <section class="card">
      <h3><i class="fas fa-dolly"></i> Entradas y Salidas de Inventario</h3>
      <div class="form-grid">
        <select v-model="formMov.id_producto">
          <option value="" disabled>Seleccionar Producto</option>
          <option v-for="p in productos" :key="p.id_producto" :value="p.id_producto">
            [{{ p.clave_proveedor }}] - {{ p.nombre }}
          </option>
        </select>
        <input type="number" v-model="formMov.cantidad" placeholder="Cantidad">
        <select v-model="formMov.estado_material">
          <option value="cerrados">Paquete Cerrado / Caja</option>
          <option value="abiertos">Pieza Abierta / Exhibición</option>
        </select>
        <select v-model="formMov.tipo">
          <option value="ENTRADA">Entrada (+)</option>
          <option value="SALIDA">Salida (-)</option>
        </select>
        <button @click="registrarMovimiento" class="btn-success">Actualizar</button>
      </div>
    </section>

    <!-- Tabla de Inventario Dual -->
    <section class="card table-card">
      <h3>Inventario Disponible</h3>
      <table class="styled-table">
        <thead>
          <tr>
            <th>Clave Prov.</th>
            <th>Material / Descripción</th>
            <th>Proveedor</th>
            <th>Stock Cerrado</th>
            <th>Stock Abierto</th>
            <th v-if="auth.user?.tipo_usuario === 'Admin'">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in productos" :key="item.id_producto">
            <td class="sku-text">{{ item.clave_proveedor }}</td>
            <td><strong>{{ item.nombre }}</strong></td>
            <td>{{ item.proveedor }}</td>
            <!-- Inventario Cerrado -->
            <td :class="item.cerrados < 3 ? 'stock-low' : 'stock-ok'">
              <i class="fas fa-box"></i> {{ item.cerrados }} cjs
            </td>
            <!-- Inventario Abierto -->
            <td class="stock-open">
              <i class="fas fa-box-open"></i> {{ item.abiertos }} pz
            </td>
            <td v-if="auth.user?.tipo_usuario === 'Admin'">
              <button @click="eliminarProducto(item.id_producto)" class="btn-icon-delete">
                <i class="fas fa-trash"></i>
              </button>
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
const nuevoProd = ref({ nombre: '', clave_proveedor: '', id_proveedor: '' });
const formMov = ref({ id_producto: '', cantidad: 0, estado_material: 'cerrados', tipo: 'ENTRADA' });

const apiBase = 'http://localhost/sistema-inventario/backend';

const cargarDatos = async () => {
  const resProd = await axios.get(`${apiBase}/productos.php`);
  productos.value = resProd.data;
  const resProv = await axios.get(`${apiBase}/usuarios.php`);
  proveedores.value = [{id_proveedor: 1, nombre: 'Distribuidora Vistawood'}]; 
};

const crearProducto = async () => {
  if(!nuevoProd.value.nombre || !nuevoProd.value.clave_proveedor || !nuevoProd.value.id_proveedor) return alert("Completa los datos");
  const res = await axios.post(`${apiBase}/productos.php`, nuevoProd.value);
  if(res.data.status === 'success') {
    nuevoProd.value = { nombre: '', clave_proveedor: '', id_proveedor: '' };
    cargarDatos();
  }
};

onMounted(cargarDatos);
</script>

<style scoped>
/* PALETA DE COLORES DECORA STUDIO */
.main-container { padding: 30px; max-width: 1300px; background: #121212; min-height: 100vh; color: #f5f5f5; }
.header h1 { color: #28a745; font-size: 1.8rem; margin-bottom: 25px; font-weight: 400; letter-spacing: 1px; }

.card { background: #1e1e1e; padding: 25px; border-radius: 14px; margin-bottom: 25px; border: 1px solid #2d2d2d; box-shadow: 0 8px 30px rgba(0,0,0,0.2); }
.admin-card { border-left: 4px solid #ffc107; }

.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; }
input, select { padding: 12px; background: #2a2a2a; border: 1px solid #3d3d3d; color: white; border-radius: 8px; font-size: 0.9rem; }
input:focus, select:focus { border-color: #28a745; outline: none; }

.styled-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.styled-table th { text-align: left; padding: 16px; border-bottom: 2px solid #333; color: #aaa; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }
.styled-table td { padding: 16px; border-bottom: 1px solid #252525; color: #ddd; }

.sku-text { font-family: 'Courier New', Courier, monospace; color: #ffc107; font-weight: bold; }

/* Indicadores de Tipo de Inventario */
.stock-ok { color: #2dce89; font-weight: 600; }
.stock-low { color: #f5365c; font-weight: 600; background: rgba(245, 54, 92, 0.1); padding: 6px; border-radius: 6px; }
.stock-open { color: #11cdef; font-weight: 600; }

.btn-success { background: #28a745; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.2s; }
.btn-success:hover { background: #218838; }
.btn-admin { background: #ffc107; color: #111; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; }
.btn-icon-delete { background: none; border: none; color: #f5365c; cursor: pointer; font-size: 1.1rem; }
</style>