<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import Card from 'primevue/card'
import FloatLabel from 'primevue/floatlabel'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'
import BrandBar from '../components/BrandBar.vue'
import { useAuth } from '../composables/useAuth.js'

const { login, isLoading } = useAuth()

const email = ref('')
const password = ref('')
const error = ref(null)

async function onSubmit() {
  error.value = null
  try {
    await login(email.value, password.value)
  } catch (e) {
    error.value = e.response?.data?.errors?.email?.[0] ?? e.response?.data?.message ?? 'Logowanie nie powiodło się'
  }
}
</script>

<template>
  <div class="auth-layout">
    <BrandBar />
    <div class="auth-page">
      <Card class="auth-card">
      <template #title>Logowanie</template>
      <template #subtitle>Witaj ponownie. Zaloguj się do Wedding Planner</template>
      <template #content>
        <form class="auth-form" @submit.prevent="onSubmit">
          <FloatLabel variant="on">
            <InputText id="login-email" v-model="email" type="email" autocomplete="email" fluid />
            <label for="login-email">Email</label>
          </FloatLabel>
          <FloatLabel variant="on">
            <Password
              id="login-password"
              v-model="password"
              autocomplete="current-password"
              :feedback="false"
              toggleMask
              fluid
            />
            <label for="login-password">Hasło</label>
          </FloatLabel>
          <Message v-if="error" severity="error">{{ error }}</Message>
          <Button type="submit" :loading="isLoading" :label="isLoading ? 'Logowanie…' : 'Zaloguj się'" fluid />
        </form>
      </template>
      <template #footer>
        <p class="auth-switch">
          Nie masz konta?
          <RouterLink to="/register">Zarejestruj się</RouterLink>
        </p>
      </template>
    </Card>
    </div>
  </div>
</template>

<style scoped>
.auth-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.auth-page {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  padding: 3rem 1rem;
}

.auth-card {
  width: 100%;
  max-width: 480px;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  margin-top: 0.5rem;
}

.auth-switch {
  margin: 0;
  text-align: center;
  font-size: 0.92rem;
}
</style>
