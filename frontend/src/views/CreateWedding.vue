<script setup>
import { ref } from 'vue'
import Card from 'primevue/card'
import FloatLabel from 'primevue/floatlabel'
import InputText from 'primevue/inputtext'
import DatePicker from 'primevue/datepicker'
import Button from 'primevue/button'
import Message from 'primevue/message'
import BrandBar from '../components/BrandBar.vue'
import { useAuth } from '../composables/useAuth.js'
import Avatar from "primevue/avatar";

const { user, logout, createWedding, isLoading, joinToWedding } = useAuth()

const mobileNavOpen = ref(false)
const name = ref('')
const date = ref(null)
const errors = ref({})

async function onSubmit() {
  errors.value = {}
  try {
    const formattedDate = date.value instanceof Date ? date.value.toISOString().slice(0, 10) : date.value
    await createWedding(name.value, formattedDate)
  } catch (e) {
    errors.value = e.response?.data?.errors ?? {}
  }
}
</script>

<template>
  <div class="auth-layout">
    <BrandBar show-menu-button @toggle-nav="mobileNavOpen = true">
      <template #end>
        <div class="brandBar-user-data">
          <div>
            <Avatar
                :label="user?.name?.[0]?.toUpperCase() ?? '?'"
                shape="circle"
                class="user-avatar"
            />
            <span class="user-name">{{user?.role_label}} {{ user?.name }}</span>
          </div>
          <div class="user-invite-code">
            <label>Kod zaproszenia: </label>
            <span>{{ user?.invite_code }}</span>
          </div>
        </div>
        <Button label="Wyloguj" icon="pi pi-sign-out" class="logout-btn" @click="logout" />
      </template>
    </BrandBar>
    <div class="auth-page">
      <Card class="auth-card">
        <template #title>Dołącz do wesela znajomego za pomocą kodu</template>
        <template #content>
          <form class="auth-form" @submit.prevent="joinToWedding">
            <div class="field-group">
              <FloatLabel variant="on">
                <InputText
                    id="wedding-name"
                    v-model="name"
                    type="text"
                    required
                    autocomplete="username"
                    :invalid="!!errors.name"
                    fluid
                />
                <label for="wedding-name">Nazwa wesela</label>
              </FloatLabel>
              <Message v-if="errors.name" severity="error" size="small" variant="simple">
                {{ errors.name[0] }}
              </Message>
            </div>
            <Button type="submit" :loading="isLoading" :label="isLoading ? 'Tworzenie…' : 'Dołącz do wesela'" fluid />
          </form>
        </template>
      </Card>
      <h1>Lub stwórz własne</h1>
      <Card class="auth-card">
        <template #title>Stwórz swoje wesele!</template>
        <template #subtitle>Zacznij planować swój ślub w Wedding Planner</template>
        <template #content>
          <form class="auth-form" @submit.prevent="createWedding">
            <div class="field-group">
              <FloatLabel variant="on">
                <InputText
                    id="wedding-name"
                    v-model="name"
                    type="text"
                    required
                    autocomplete="username"
                    :invalid="!!errors.name"
                    fluid
                />
                <label for="wedding-name">Nazwa wesela</label>
              </FloatLabel>
              <Message v-if="errors.name" severity="error" size="small" variant="simple">
                {{ errors.name[0] }}
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
                    :invalid="!!errors.date"
                    fluid
                />
                <label for="wedding-date">Data wesela</label>
              </FloatLabel>
              <Message v-if="errors.date" severity="error" size="small" variant="simple">
                {{ errors.date[0] }}
              </Message>
            </div>
            <Button type="submit" :loading="isLoading" :label="isLoading ? 'Tworzenie…' : 'Stwórz wesele'" fluid />
          </form>
        </template>
      </Card>
    </div>
  </div>
</template>

<style scoped>
.user-name, .user-invite-code {
  font-weight: 600;
  margin: 0 0.25rem;
  color: #000000;
}

.brandBar-user-data {
  text-align: center;
}

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

.auth-switch {
  margin: 0;
  text-align: center;
  font-size: 0.92rem;
}
</style>
