<template>
  <div class="main-container">
    <header class="header">
      <h1><i class="fas fa-history"></i> Historial de Movimientos</h1>
    </header>

    <section class="card">
      <div class="table-header-flex">
        <h3>Bitácora de Entradas y Salidas</h3>
        <div class="search-box">
          <i class="fas fa-search search-icon"></i>
          <input v-model="buscarHistorial" placeholder="Buscar por material o proyecto..." class="input-search">
        </div>
      </div>

      <table class="styled-table">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Clave</th>
            <th>Material</th>
            <th>Cantidad</th>
            <th>Proyecto / Destino</th>
            <th>Responsable</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="mov in historialFiltrado" :key="mov.id_movimiento || mov.ID_MOVIMIENTO">
            <td>{{ mov.fecha || mov.FECHA }}</td>
            <td>
              <span :class="['type-badge', (mov.tipo || mov.TIPO) === 'ENTRADA' ? 'badge-in' : 'badge-out']">
                {{ mov.tipo || mov.TIPO }}
              </span>
            </td>
            <td class="sku-text">{{ mov.clave_proveedor || mov.CLAVE_PROVEEDOR || 'S/C' }}</td>
            <td><strong>{{ mov.producto || mov.PRODUCTO }}</strong></td>
            <td>{{ mov.cantidad || mov.CANTIDAD }} ud</td>
            <td class="project-text">{{ mov.proyecto || mov.PROYECTO || 'Stock General' }}</td>
            <td>{{ mov.usuario || mov.USUARIO }}</td>
          </tr>
          <tr v-if="historialFiltrado.length === 0">
            <td colspan="7" class="text-center text-muted">No hay registros que coincidan con la búsqueda.</td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const historial = ref([]);
const buscarHistorial = ref('');
const url = 'http://localhost/sistema-inventario/backend/historial.php';

const cargarHistorial = async () => {
  try {
    const res = await axios.get(url);
    historial.value = res.data;
  } catch (err) {
    console.error("Error al cargar la bitácora:", err);
  }
};

// Filtro computado para buscar instantáneamente en el historial
const historialFiltrado = computed(() => {
  if (!buscarHistorial.value) {
    return historial.value;
  }
  const termino = buscarHistorial.value.toLowerCase().trim();
  return historial.value.filter(mov => {
    const producto = (mov.producto || mov.PRODUCTO || '').toLowerCase();
    const proyecto = (mov.proyecto || mov.PROYECTO || '').toLowerCase();
    const clave = (mov.clave_proveedor || mov.CLAVE_PROVEEDOR || '').toLowerCase();
    return producto.includes(termino) || proyecto.includes(termino) || clave.includes(termino);
  });
});

onMounted(cargarHistorial);
</script>

<style scoped>
.main-container { padding: 30px; background: #121212; min-height: 100vh; color: #f5f5f5; }
.header h1 { color: #28a745; font-size: 1.8rem; margin-bottom: 25px; font-weight: 400; letter-spacing: 1px; }
.card { background: #1e1e1e; padding: 25px; border-radius: 14px; border: 1px solid #2d2d2d; box-shadow: 0 8px 30px rgba(0,0,0,0.2); }

.table-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 15px; }
.search-box { position: relative; display: flex; align-items: center; width: 320px; }
.search-icon { position: absolute; left: 12px; color: #777; }
.input-search { width: 100%; padding: 10px 10px 10px 38px !important; background: #2a2a2a; border: 1px solid #3d3d3d; color: white; border-radius: 8px; font-size: 0.85rem; }
.input-search:focus { border-color: #28a745; outline: none; }

.styled-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.styled-table th { padding: 16px; border-bottom: 2px solid #333; color: #aaa; font-size: 0.75rem; text-transform: uppercase; text-align: left; letter-spacing: 1px; }
.styled-table td { padding: 16px; border-bottom: 1px solid #252525; color: #ddd; }

.type-badge { padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; }
.badge-in { background: rgba(45, 206, 137, 0.15); color: #2dce89; border: 1px solid #2dce89; }
.badge-out { background: rgba(245, 54, 92, 0.15); color: #f5365c; border: 1px solid #f5365c; }
.project-text { color: #11cdef; font-style: italic; }
.sku-text { font-family: monospace; color: #ffc107; font-weight: bold; }
.text-center { text-align: center !important; padding: 25px !important; }
.text-muted { color: #777; }
</style>