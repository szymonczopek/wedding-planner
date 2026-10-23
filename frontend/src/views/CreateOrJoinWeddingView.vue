<script setup>
import { ref } from 'vue'
import Card from 'primevue/card'
import FloatLabel from 'primevue/floatlabel'
import InputText from 'primevue/inputtext'
import DatePicker from 'primevue/datepicker'
import Button from 'primevue/button'
import Message from 'primevue/message'
import Select from 'primevue/select'
import AuthLayout from '../layouts/AuthLayout.vue'
import { useWedding } from '../composables/useWedding.js'

const { createWedding, joinToWedding, isCreating, isJoining} = useWedding()

const name = ref('')
const invite_code = ref('')
const date = ref(null)
const createErrors = ref({})
const joinErrors = ref({})
const joiningSelectedRole = ref(null);
const creatingSelectedRole = ref(null);
const roles = ref([
  { label: 'Pan młody', key: 'groom' },
  { label: 'Panna młoda', key: 'bride' },
  { label: 'Konsultant ślubny', key: 'wedding_planner' }
]);

async function create() {
  createErrors.value = {}
  try {
    const formattedDate = date.value instanceof Date ? date.value.toISOString().slice(0, 10) : date.value
    await createWedding(name.value, formattedDate, creatingSelectedRole.value)
  } catch (e) {
    createErrors.value = e.response?.data?.errors ?? {}
  }
}

async function join() {
  joinErrors.value = {}
  try {
    await joinToWedding(invite_code.value, joiningSelectedRole.value)
  } catch (e) {
    joinErrors.value = e.response?.data?.errors ?? {}
  }
}

</script>

<template>
  <AuthLayout>
    <div class="auth-page">
      <Card class="auth-card">
        <template #title>Dołącz do wesela za pomocą kodu zaproszenia</template>
        <template #content>
          <form class="auth-form" @submit.prevent="join()">
            <div class="field-group">
              <FloatLabel variant="on">
                <InputText
                    id="invite_code"
                    v-model="invite_code"
                    type="text"
                    :invalid="!!joinErrors.invite_code"
                    fluid
                />
                <label for="invite_code">Kod wesela</label>
              </FloatLabel>
              <Message v-if="joinErrors.invite_code" severity="error" size="small" variant="simple">
                {{ joinErrors.invite_code[0] }}
              </Message>
              <FloatLabel variant="on">
                <Select
                    v-model="joiningSelectedRole"
                    :options="roles"
                    optionLabel="label"
                    optionValue="key"
                    fluid
                />
                <label for="user-role-confirmation">Rola</label>
              </FloatLabel>
              <Message v-if="joinErrors.role" severity="error" size="small" variant="simple">
                {{ joinErrors.role[0] }}
              </Message>
            </div>
            <Button type="submit" :loading="isJoining" :label="isJoining ? 'Dołączanie…' : 'Dołącz do wesela'" fluid />
          </form>
        </template>
      </Card>
      lub stwórz własne
      <Card class="auth-card">
        <template #title>Stwórz swoje wesele!</template>
        <template #subtitle>Zacznij planować swój ślub w Wedding Planner</template>
        <template #content>
          <form class="auth-form" @submit.prevent="create()">
            <div class="field-group">
              <FloatLabel variant="on">
                <InputText
                    id="wedding-name"
                    v-model="name"
                    type="text"
                    autocomplete="username"
                    :invalid="!!createErrors.name"
                    fluid
                />
                <label for="wedding-name">Nazwa wesela</label>
              </FloatLabel>
              <Message v-if="createErrors.name" severity="error" size="small" variant="simple">
                {{ createErrors.name[0] }}
              </Message>
            </div>
            <div class="field-group">
              <FloatLabel variant="on">
                <DatePicker
                    inputId="wedding-date"
                    v-model="date"
                    dateFormat="yy-mm-dd"
                    showIcon
                    iconDisplay="input"
                    :invalid="!!createErrors.date"
                    fluid
                />
                <label for="wedding-date">Data wesela</label>
              </FloatLabel>
              <Message v-if="createErrors.date" severity="error" size="small" variant="simple">
                {{ createErrors.date[0] }}
              </Message>
              <FloatLabel variant="on">
                <Select
                    v-model="creatingSelectedRole"
                    :options="roles"
                    optionLabel="label"
                    optionValue="key"
                    fluid
                />
                <label for="user-role-confirmation">Rola</label>
              </FloatLabel>
              <Message v-if="createErrors.role" severity="error" size="small" variant="simple">
                {{ createErrors.role[0] }}
              </Message>
            </div>
            <Button type="submit" :loading="isCreating" :label="isCreating ? 'Tworzenie…' : 'Stwórz wesele'" fluid />
          </form>
        </template>
      </Card>
    </div>
  </AuthLayout>
</template>

<style scoped>
.auth-page {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-around;
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
</style>
