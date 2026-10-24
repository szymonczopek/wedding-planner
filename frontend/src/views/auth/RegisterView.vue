<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import AuthLayout from '../../layouts/AuthLayout.vue'
import FormField from '../../components/forms/FormField.vue'
import { useAuth } from '../../composables/useAuth.js'
import { getValidationErrors } from '../../api/errors.js'

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
    errors.value = getValidationErrors(e)
  }
}
</script>

<template>
  <AuthLayout>
    <Card class="auth-card">
      <template #title>Rejestracja</template>
      <template #subtitle>Załóż konto i zacznij planować swój ślub w Wedding Planner</template>
      <template #content>
        <form class="auth-form" @submit.prevent="onSubmit">
          <FormField id="register-name" label="Nazwa użytkownika" :error="errors.name?.[0]">
            <template #default="{ id, invalid }">
              <InputText
                :id="id"
                v-model="name"
                type="text"
                autocomplete="username"
                :invalid="invalid"
                fluid
              />
            </template>
          </FormField>
          <FormField id="register-email" label="Email" :error="errors.email?.[0]">
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
          <FormField id="register-password" label="Hasło" :error="errors.password?.[0]">
            <template #default="{ id, invalid }">
              <Password
                :id="id"
                v-model="password"
                autocomplete="new-password"
                toggleMask
                :invalid="invalid"
                fluid
              />
            </template>
          </FormField>
          <FormField
            id="register-password-confirmation"
            label="Powtórz hasło"
            :error="errors.password?.[0]"
          >
            <template #default="{ id, invalid }">
              <Password
                :id="id"
                v-model="passwordConfirmation"
                autocomplete="new-password"
                :feedback="false"
                toggleMask
                :invalid="invalid"
                fluid
              />
            </template>
          </FormField>
          <Button type="submit" :loading="isLoading" :label="isLoading ? 'Rejestracja…' : 'Załóż konto'" fluid />
        </form>
      </template>
      <template #footer>
        <p class="auth-switch">
          Masz już konto?
          <RouterLink to="/login">Zaloguj się</RouterLink>
        </p>
      </template>
    </Card>
  </AuthLayout>
</template>
