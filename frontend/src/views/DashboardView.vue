<template>
  <div class="main-container">
    <header class="header">
      <h1><i class="fas fa-layer-group"></i> Control de Almacén Decora</h1>
    </header>

    <section v-if="auth.user?.tipo_usuario === 'Admin' || auth.user?.tipo_usuario === 'UsuarioComun'" class="card admin-card">
      <h3><i class="fas fa-tag"></i> Registrar Producto con Clave</h3>
      <div class="form-grid">
        <input v-model="nuevoProd.nombre" placeholder="Nombre del Material">
        <input v-model="nuevoProd.clave_proveedor" placeholder="Clave de Proveedor (SKU)">
        <select v-model="nuevoProd.id_proveedor">
          <option value="" disabled>Seleccionar Proveedor</option>
          <option v-for="p in proveedores" :key="p.id_proveedor" :value="p.id_proveedor">
            {{ p.nombre }}
          </option>
        </select>
        <button @click="crearProducto" class="btn-admin">Guardar Código</button>
      </div>
    </section>

    <section class="card">
      <h3><i class="fas fa-dolly"></i> Entradas y Salidas de Inventario</h3>
      <div class="form-grid">
        <select v-model="formMov.id_producto">
          <option value="" disabled>Seleccionar Producto</option>
          <option v-for="p in productos" :key="p.id_producto || p.ID_PRODUCTO" :value="p.id_producto || p.ID_PRODUCTO">
            [{{ p.clave_proveedor || p.CLAVE_PROVEEDOR || 'S/C' }}] - {{ p.nombre || p.NOMBRE }}
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

    <section class="card table-card">
      <div class="table-header-flex">
        <h3>Inventario Disponible</h3>
        <div class="search-box">
          <i class="fas fa-search search-icon"></i>
          <input v-model="buscarTermino" placeholder="Buscar por material o clave (SKU)..." class="input-search">
        </div>
      </div>

      <table class="styled-table">
        <thead>
          <tr>
            <th>Clave Prov.</th>
            <th>Material / Descripción</th>
            <th>Proveedor</th>
            <th>Stock Cerrado</th>
            <th>Stock Abierto</th>
            <th v-if="auth.user?.tipo_usuario === 'Admin' || auth.user?.tipo_usuario === 'UsuarioComun'">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in productosFiltrados" :key="item.id_producto || item.ID_PRODUCTO">
            <td class="sku-text">{{ item.clave_proveedor || item.CLAVE_PROVEEDOR }}</td>
            <td><strong>{{ item.nombre || item.NOMBRE }}</strong></td>
            <td>{{ item.proveedor || item.PROVEEDOR }}</td>
            
            <td :class="(item.cerrados || item.CERRADOS || 0) < 3 ? 'stock-low' : 'stock-ok'">
              <i class="fas fa-box"></i> {{ item.cerrados !== undefined ? item.cerrados : item.CERRADOS }} cjs
            </td>
            
            <td class="stock-open">
              <i class="fas fa-box-open"></i> {{ item.abiertos !== undefined ? item.abiertos : item.ABIERTOS }} pz
            </td>
            
            <td v-if="auth.user?.tipo_usuario === 'Admin' || auth.user?.tipo_usuario === 'UsuarioComun'">
              <button @click="eliminarProducto(item.id_producto || item.ID_PRODUCTO)" class="btn-icon-delete">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
          <tr v-if="productosFiltrados.length === 0">
            <td colspan="6" class="text-center text-muted">No se encontraron materiales que coincidan.</td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const productos = ref([]);
const proveedores = ref([]);
const buscarTermino = ref('');

const nuevoProd = ref({ nombre: '', clave_proveedor: '', id_proveedor: '' });
const formMov = ref({ id_producto: '', cantidad: 0, estado_material: 'cerrados', tipo: 'ENTRADA' });

const apiBase = 'http://localhost/sistema-inventario/backend';

const cargarDatos = async () => {
  try {
    const resProd = await axios.get(`${apiBase}/productos.php`);
    productos.value = resProd.data;
    
    // Lista local estática de proveedores para sincronizar rápido
    proveedores.value = [
      { id_proveedor: 1, nombre: 'Distribuidora Vistawood' },
      { id_proveedor: 2, nombre: 'Materiales del Golfo' },
      { id_proveedor: 3, nombre: 'Maderas Finas Ver' }
    ];
  } catch (error) {
    console.error("Error al cargar los datos:", error);
  }
};

// Filtro inteligente para el buscador (No le importa si es mayúscula o minúscula)
const productosFiltrados = computed(() => {
  if (!buscarTermino.value) {
    return productos.value;
  }
  const termino = buscarTermino.value.toLowerCase().trim();
  return productos.value.filter(item => {
    const nombre = (item.nombre || item.NOMBRE || '').toLowerCase();
    const clave = (item.clave_proveedor || item.CLAVE_PROVEEDOR || '').toLowerCase();
    return nombre.includes(termino) || clave.includes(termino);
  });
});

const crearProducto = async () => {
  if(!nuevoProd.value.nombre || !nuevoProd.value.clave_proveedor || !nuevoProd.value.id_proveedor) {
    return alert("Por favor completa todos los campos.");
  }
  try {
    const res = await axios.post(`${apiBase}/productos.php`, nuevoProd.value);
    if(res.data.status === 'success' || !res.data.status) {
      alert("¡Producto registrado con éxito!");
      nuevoProd.value = { nombre: '', clave_proveedor: '', id_proveedor: '' };
      await cargarDatos();
    }
  } catch (err) {
    alert("Error al guardar el producto");
  }
};

const registrarMovimiento = async () => {
  if(!formMov.value.id_producto || formMov.value.cantidad <= 0) {
    return alert("Selecciona un producto y una cantidad válida.");
  }
  try {
    const res = await axios.post(`${apiBase}/movimientos.php`, formMov.value);
    if(res.data.status === 'success' || !res.data.status) {
      alert("Stock actualizado correctamente.");
      formMov.value.cantidad = 0;
      await cargarDatos();
    }
  } catch (err) {
    alert("Error al procesar movimiento");
  }
};

const eliminarProducto = async (id) => {
  if (!id) return alert("ID inválido.");
  if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
    try {
      const res = await axios.delete(`${apiBase}/productos.php?id=${id}`);
      if(res.data.status === 'success' || !res.data.status) {
        await cargarDatos();
      }
    } catch (err) {
      alert("Error al eliminar.");
    }
  }
};

onMounted(cargarDatos);
</script>

<style scoped>
.main-container { padding: 30px; max-width: 1300px; background: #121212; min-height: 100vh; color: #f5f5f5; }
.header h1 { color: #28a745; font-size: 1.8rem; margin-bottom: 25px; font-weight: 400; letter-spacing: 1px; }

.card { background: #1e1e1e; padding: 25px; border-radius: 14px; margin-bottom: 25px; border: 1px solid #2d2d2d; box-shadow: 0 8px 30px rgba(0,0,0,0.2); }
.admin-card { border-left: 4px solid #ffc107; }

.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; }
input, select { padding: 12px; background: #2a2a2a; border: 1px solid #3d3d3d; color: white; border-radius: 8px; font-size: 0.9rem; }
input:focus, select:focus { border-color: #28a745; outline: none; }

.table-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 15px; }
.search-box { position: relative; display: flex; align-items: center; width: 320px; }
.search-icon { position: absolute; left: 12px; color: #777; }
.input-search { width: 100%; padding: 10px 10px 10px 38px !important; background: #2a2a2a; border: 1px solid #3d3d3d; color: white; border-radius: 8px; font-size: 0.85rem; }
.input-search:focus { border-color: #28a745; outline: none; }

.styled-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.styled-table th { text-align: left; padding: 16px; border-bottom: 2px solid #333; color: #aaa; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }
.styled-table td { padding: 16px; border-bottom: 1px solid #252525; color: #ddd; }

.sku-text { font-family: monospace; color: #ffc107; font-weight: bold; }
.stock-ok { color: #2dce89; font-weight: 600; }
.stock-low { color: #f5365c; font-weight: 600; background: rgba(245, 54, 92, 0.1); padding: 6px; border-radius: 6px; }
.stock-open { color: #11cdef; font-weight: 600; }

.btn-success { background: #28a745; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.2s; }
.btn-success:hover { background: #218838; }
.btn-admin { background: #ffc107; color: #111; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; }
.btn-icon-delete { background: none; border: none; color: #f5365c; cursor: pointer; font-size: 1.1rem; }
.text-center { text-align: center !important; padding: 25px !important; }
.text-muted { color: #777; }
</style>