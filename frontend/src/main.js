import { createApp } from 'vue'
import './assets/main.css'
import App from './App.vue'
import router from './router/index.js'
import { useAuth } from './composables/useAuth.js'

const app = createApp(App)
app.use(router)

const { fetchUser } = useAuth()
fetchUser().finally(() => {
  app.mount('#app')
})
