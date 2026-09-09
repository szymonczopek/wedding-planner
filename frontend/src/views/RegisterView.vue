<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuth } from '../composables/useAuth.js'
import '../assets/auth.css'

const { register, isLoading } = useAuth()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const errors = ref({})

async function onSubmit() {
  errors.value = {}
  try {
    await register(name.value, email.value, password.value, passwordConfirmation.value)
  } catch (e) {
    errors.value = e.response?.data?.errors ?? {}
  }
}
</script>

<template>
  <div class="auth-page">
    <div class="auth-card">
      <h1 class="auth-title">Rejestracja</h1>
      <p class="auth-subtitle">Załóż konto i zacznij planować swój ślub</p>
      <form class="auth-form" @submit.prevent="onSubmit">
        <label class="auth-field">
          Nazwa użytkownika
          <input v-model="name" type="text" required autocomplete="username" placeholder="np. Anna" />
          <small class="field-error" v-if="errors.name">{{ errors.name[0] }}</small>
        </label>
        <label class="auth-field">
          Email
          <input v-model="email" type="email" required autocomplete="email" placeholder="np. anna@przyklad.pl" />
          <small class="field-error" v-if="errors.email">{{ errors.email[0] }}</small>
        </label>
        <label class="auth-field">
          Hasło
          <input v-model="password" type="password" required autocomplete="new-password" placeholder="min. 8 znaków" />
          <small class="field-error" v-if="errors.password">{{ errors.password[0] }}</small>
        </label>
        <label class="auth-field">
          Powtórz hasło
          <input v-model="passwordConfirmation" type="password" required autocomplete="new-password" placeholder="••••••••" />
        </label>
        <button class="auth-button" type="submit" :disabled="isLoading">
          {{ isLoading ? 'Rejestracja…' : 'Załóż konto' }}
        </button>
      </form>
      <p class="auth-switch">
        Masz już konto?
        <RouterLink class="auth-link" to="/login">Zaloguj się</RouterLink>
      </p>
    </div>
  </div>
</template>