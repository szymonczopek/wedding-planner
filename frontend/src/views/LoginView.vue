<script setup>
import {ref} from 'vue'
import {RouterLink} from 'vue-router'
import {useAuth} from '../composables/useAuth.js'
import '../assets/auth.css'

const {login, isLoading} = useAuth()

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
  <div class="auth-page">
    <div class="auth-card">
      <h1 class="auth-title">Logowanie</h1>
      <p class="auth-subtitle">Witaj ponownie. Zaloguj się do Wedding Planner by Szymon Czopek</p>
      <form class="auth-form" @submit.prevent="onSubmit">
        <label class="auth-field">
          Email
          <input v-model="email" type="email" required autocomplete="email" placeholder="np. anna@przyklad.pl" />
        </label>
        <label class="auth-field">
          Hasło
          <input v-model="password" type="password" required autocomplete="current-password" placeholder="••••••••" />
        </label>
        <p v-if="error" class="auth-error">{{ error }}</p>
        <button class="auth-button" type="submit" :disabled="isLoading">
          {{ isLoading ? 'Logowanie…' : 'Zaloguj się' }}
        </button>
      </form>
      <p class="auth-switch">
        Nie masz konta?
        <RouterLink class="auth-link" to="/register">Zarejestruj się</RouterLink>
      </p>
    </div>
  </div>
</template>

<style scoped>

</style>