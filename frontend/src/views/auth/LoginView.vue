<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'
import AuthLayout from '../../layouts/AuthLayout.vue'
import FormField from '../../components/forms/FormField.vue'
import { useAuth } from '../../composables/useAuth.js'
import { getErrorMessage } from '../../api/errors.js'

const { login, isLoading } = useAuth()

const email = ref('')
const password = ref('')
const error = ref(null)

async function onSubmit() {
  error.value = null
  try {
    await login(email.value, password.value)
  } catch (e) {
    error.value = getErrorMessage(e, 'Logowanie nie powiodło się')
  }
}
</script>

<template>
  <AuthLayout>
    <Card class="auth-card">
      <template #title>Logowanie</template>
      <template #subtitle>Witaj ponownie. Zaloguj się do Wedding Planner</template>
      <template #content>
        <form class="auth-form" @submit.prevent="onSubmit">
          <FormField id="login-email" label="Email">
            <template #default="{ id, invalid }">
              <InputText
                :id="id"
                v-model="email"
                type="email"
                autocomplete="email"
                :invalid="invalid"
                fluid
              />
            </template>
          </FormField>
          <FormField id="login-password" label="Hasło">
            <template #default="{ id, invalid }">
              <Password
                :id="id"
                v-model="password"
                autocomplete="current-password"
                :feedback="false"
                toggleMask
                :invalid="invalid"
                fluid
              />
            </template>
          </FormField>
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
  </AuthLayout>
</template>
