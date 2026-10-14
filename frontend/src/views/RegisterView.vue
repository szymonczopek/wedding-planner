<script setup>
import {ref} from 'vue'
import {RouterLink} from 'vue-router'
import Card from 'primevue/card'
import FloatLabel from 'primevue/floatlabel'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'
import BrandBar from '../components/BrandBar.vue'
import {useAuth} from '../composables/useAuth.js'

const {register, isLoading} = useAuth()

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
  <div class="auth-layout">
    <BrandBar/>
    <div class="auth-page">
      <Card class="auth-card">
        <template #title>Rejestracja</template>
        <template #subtitle>Załóż konto i zacznij planować swój ślub w Wedding Planner</template>
        <template #content>
          <form class="auth-form" @submit.prevent="onSubmit()">
            <div class="field-group">
              <FloatLabel variant="on">
                <InputText
                    id="register-name"
                    v-model="name"
                    type="text"
                    autocomplete="username"
                    :invalid="!!errors.name"
                    fluid
                />
                <label for="register-name">Nazwa użytkownika</label>
              </FloatLabel>
              <Message v-if="errors.name" severity="error" size="small" variant="simple">
                {{ errors.name[0] }}
              </Message>
            </div>
            <div class="field-group">
              <FloatLabel variant="on">
                <InputText
                    id="register-email"
                    v-model="email"
                    type="email"
                    autocomplete="email"
                    :invalid="!!errors.email"
                    fluid
                />
                <label for="register-email">Email</label>
              </FloatLabel>
              <Message v-if="errors.email" severity="error" size="small" variant="simple">
                {{ errors.email[0] }}
              </Message>
            </div>
            <div class="field-group">
              <FloatLabel variant="on">
                <Password
                    id="register-password"
                    v-model="password"
                    autocomplete="new-password"
                    toggleMask
                    :invalid="!!errors.password"
                    fluid
                />
                <label for="register-password">Hasło</label>
              </FloatLabel>
              <Message v-if="errors.password" severity="error" size="small" variant="simple">
                {{ errors.password[0] }}
              </Message>
            </div>
            <div class="field-group">
              <FloatLabel variant="on">
                <Password
                    id="register-password-confirmation"
                    v-model="passwordConfirmation"
                    autocomplete="new-password"
                    :feedback="false"
                    toggleMask
                    fluid
                />
                <label for="register-password-confirmation">Powtórz hasło</label>
              </FloatLabel>
              <Message v-if="errors.password" severity="error" size="small" variant="simple">
                {{ errors.password[0] }}
              </Message>
            </div>
            <Button type="submit" :loading="isLoading" :label="isLoading ? 'Rejestracja…' : 'Załóż konto'" fluid/>
          </form>
        </template>
        <template #footer>
          <p class="auth-switch">
            Masz już konto?
            <RouterLink to="/login">Zaloguj się</RouterLink>
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

.field-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.auth-switch {
  margin: 0;
  text-align: center;
  font-size: 0.92rem;
}
</style>
