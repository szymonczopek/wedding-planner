<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/client.js'

const backendStatus = ref('Sprawdzam połączenie z backendem...')
const backendData = ref(null)
const error = ref(null)

async function checkBackend() {
  backendStatus.value = 'Łączenie z /api/health...'
  error.value = null
  try {
    const { data } = await api.get('/health')
    backendStatus.value = '✅ Połączono z Laravel API'
    backendData.value = data
  } catch (e) {
    backendStatus.value = '❌ Brak połączenia z backendem'
    error.value = e.response?.data || e.message
    console.error(e)
  }
}

onMounted(checkBackend)
</script>

<template>
  <div>
    <h1>Wedding Planner - Vue 3 + Composition API</h1>
    <p>Frontend łączy się z backendem przez <code>nginx:80/api -&gt; php:9000</code> (Opcja 1, brak CORS)</p>

    <section style="margin-top:2rem; padding:1rem; border:1px solid #ddd; border-radius:8px;">
      <h2>Status API</h2>
      <p><strong>{{ backendStatus }}</strong></p>
      <button @click="checkBackend" style="padding:0.5rem 1rem; cursor:pointer;">Sprawdź ponownie</button>
      <pre v-if="backendData" style="background:#f5f5f5; padding:1rem; margin-top:1rem; overflow:auto;">{{ JSON.stringify(backendData, null, 2) }}</pre>
      <pre v-if="error" style="background:#fee; padding:1rem; margin-top:1rem; overflow:auto; color:#900;">{{ JSON.stringify(error, null, 2) }}</pre>
      <p v-if="error" style="color:#666; font-size:0.9rem;">
        Upewnij się że Laravel ma route <code>GET /api/health</code> (zobacz backend/routes/api.php)
      </p>
    </section>

    <section style="margin-top:2rem;">
      <h3>Jak to działa?</h3>
      <ul>
        <li><code>axios.get('/api/health')</code> → przeglądarka wysyła na <code>http://localhost/api/health</code></li>
        <li>Nginx (port 80) widzi prefix <code>/api/</code> → przekazuje do <code>php:9000</code> (Laravel)</li>
        <li>CORS nie jest potrzebny bo frontend i backend mają ten sam origin (<code>http://localhost</code>)</li>
      </ul>
    </section>
  </div>
</template>
