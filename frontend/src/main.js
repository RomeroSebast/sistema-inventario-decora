import { createApp } from 'vue'
import { createPinia } from 'pinia' // Importamos Pinia
import App from './App.vue'
import router from './router' // Importamos tu configuración de rutas

const app = createApp(App)

app.use(createPinia()) // Activamos Pinia
app.use(router)        // Activamos el Router

app.mount('#app')