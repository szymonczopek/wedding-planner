<script setup>
import { ref } from 'vue'
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import DatePicker from 'primevue/datepicker'
import Button from 'primevue/button'
import Select from 'primevue/select'
import AuthLayout from '../../layouts/AuthLayout.vue'
import FormField from '../../components/forms/FormField.vue'
import { useWedding } from '../../composables/useWedding.js'
import { getValidationErrors } from '../../api/errors.js'

const { createWedding, joinToWedding, isCreating, isJoining } = useWedding()

const name = ref('')
const invite_code = ref('')
const date = ref(null)
const createErrors = ref({})
const joinErrors = ref({})
const joiningSelectedRole = ref(null)
const creatingSelectedRole = ref(null)
const roles = ref([
  { label: 'Pan młody', key: 'groom' },
  { label: 'Panna młoda', key: 'bride' },
  { label: 'Konsultant ślubny', key: 'wedding_planner' },
])

async function create() {
  createErrors.value = {}
  try {
    const formattedDate = date.value instanceof Date ? date.value.toISOString().slice(0, 10) : date.value
    await createWedding(name.value, formattedDate, creatingSelectedRole.value)
  } catch (e) {
    createErrors.value = getValidationErrors(e)
  }
}

async function join() {
  joinErrors.value = {}
  try {
    await joinToWedding(invite_code.value, joiningSelectedRole.value)
  } catch (e) {
    joinErrors.value = getValidationErrors(e)
  }
}
</script>

<template>
  <AuthLayout spread>
    <Card class="auth-card">
      <template #title>Dołącz do wesela za pomocą kodu zaproszenia</template>
      <template #content>
        <form class="auth-form" @submit.prevent="join">
          <FormField id="invite-code" label="Kod wesela" :error="joinErrors.invite_code?.[0]">
            <template #default="{ id, invalid }">
              <InputText
                :id="id"
                v-model="invite_code"
                type="text"
                :invalid="invalid"
                fluid
              />
            </template>
          </FormField>
          <FormField id="join-role" label="Rola" :error="joinErrors.role?.[0]">
            <template #default="{ id, invalid }">
              <Select
                :inputId="id"
                v-model="joiningSelectedRole"
                :options="roles"
                optionLabel="label"
                optionValue="key"
                :invalid="invalid"
                fluid
              />
            </template>
          </FormField>
          <Button type="submit" :loading="isJoining" :label="isJoining ? 'Dołączanie…' : 'Dołącz do wesela'" fluid />
        </form>
      </template>
    </Card>
    lub stwórz własne
    <Card class="auth-card">
      <template #title>Stwórz swoje wesele!</template>
      <template #subtitle>Zacznij planować swój ślub w Wedding Planner</template>
      <template #content>
        <form class="auth-form" @submit.prevent="create">
          <FormField id="wedding-name" label="Nazwa wesela" :error="createErrors.name?.[0]">
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
          <FormField id="wedding-date" label="Data wesela" :error="createErrors.date?.[0]">
            <template #default="{ id, invalid }">
              <DatePicker
                :inputId="id"
                v-model="date"
                dateFormat="yy-mm-dd"
                showIcon
                iconDisplay="input"
                :invalid="invalid"
                fluid
              />
            </template>
          </FormField>
          <FormField id="create-role" label="Rola" :error="createErrors.role?.[0]">
            <template #default="{ id, invalid }">
              <Select
                :inputId="id"
                v-model="creatingSelectedRole"
                :options="roles"
                optionLabel="label"
                optionValue="key"
                :invalid="invalid"
                fluid
              />
            </template>
          </FormField>
          <Button type="submit" :loading="isCreating" :label="isCreating ? 'Tworzenie…' : 'Stwórz wesele'" fluid />
        </form>
      </template>
    </Card>
  </AuthLayout>
</template>
