<template>
  <div class="container">
    <header class="view-header">
      <h1>HISTORIAL DE MOVIMIENTOS</h1>
      <p>REGISTRO CRONOLÓGICO DE ENTRADAS Y SALIDAS</p>
    </header>

    <section class="card">
      <div class="table-container">
        <table class="history-table">
          <thead>
            <tr>
              <th>TIPO</th>
              <th>PRODUCTO</th>
              <th>CANTIDAD</th>
              <th>FECHA</th>
              <th>RESPONSABLE</th>
              <th>PROYECTO / DESTINO</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in historial" :key="index">
              <td>
                <span :class="['badge', item.tipo === 'ENTRADA' ? 'bg-entrada' : 'bg-salida']">
                  {{ item.tipo }}
                </span>
              </td>
              <td class="font-bold">{{ item.producto }}</td>
              <td>{{ item.cantidad }} pz</td>
              <td>{{ formatFecha(item.fecha) }}</td>
              <td>{{ item.responsable }}</td>
              <td>
                <span class="proyecto-text">{{ item.proyecto || 'N/A' }}</span>
              </td>
            </tr>
            <tr v-if="historial.length === 0">
              <td colspan="6" class="text-center">NO HAY MOVIMIENTOS REGISTRADOS</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const historial = ref([]);

const cargarHistorial = async () => {
  try {
    const url = 'http://localhost/sistema-inventario/backend/historial.php';
    const res = await axios.get(url);
    historial.value = res.data;
  } catch (error) {
    console.error("ERROR AL CARGAR EL HISTORIAL:", error);
  }
};

// Función para darle un formato más limpio a la fecha
const formatFecha = (fechaStr) => {
  if (!fechaStr) return '';
  const date = new Date(fechaStr);
  return date.toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  });
};

onMounted(cargarHistorial);
</script>

<style scoped>
.container { max-width: 1100px; margin: auto; }
.view-header { margin-bottom: 25px; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
.view-header h1 { margin: 0; color: #333; }

.card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }

.history-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.history-table th { background: #f8f9fa; padding: 15px; text-align: left; border-bottom: 2px solid #dee2e6; color: #555; }
.history-table td { padding: 15px; border-bottom: 1px solid #eee; color: #444; }

.badge { padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; color: white; }
.bg-entrada { background-color: #28a745; }
.bg-salida { background-color: #dc3545; }

.font-bold { font-weight: bold; color: #222; }
.proyecto-text { color: #666; font-style: italic; }
.text-center { text-align: center; padding: 30px; color: #999; }
</style>