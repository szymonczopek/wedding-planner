<script setup>
import {computed, onMounted, ref} from 'vue'
import router from '../router/index.js'
import Button from 'primevue/button'
import BrandBar from '../components/BrandBar.vue'
import {useAuth} from '../composables/useAuth.js'
import {useWedding} from '../composables/useWedding.js'
import Card from "primevue/card";

const {user, logout, getUserWithAllWeddings} = useAuth()
const {chooseWedding} = useWedding()

const mobileNavOpen = ref(false)
const name = ref('')
const invite_code = ref('')
const date = ref(null)
const userWithAllWeddings = ref(null)
const chooseErrors = ref({})

onMounted(async () => {
  userWithAllWeddings.value = await getUserWithAllWeddings()
})

const weddings = computed(() => userWithAllWeddings.value?.weddings ?? [])

const selectedWeddings = user.value.weddings ?? []
const hasSelectedWedding = selectedWeddings.length > 0

async function choose(id) {
  chooseErrors.value = {}
  try {
    await chooseWedding(id)
  } catch (e) {
    chooseErrors.value = e.response?.data?.errors ?? {}
  }
}

async function desktop() {
  await router.push('/')
}

</script>

<template>
  <div class="auth-layout">
    <BrandBar show-menu-button @toggle-nav="mobileNavOpen = true">
      <template #end>
        <Button v-if="hasSelectedWedding" label="Pulpit" icon="pi pi-home" class="btn" @click="desktop"/>
        <Button label="Wyloguj" icon="pi pi-sign-out" class="logout-btn" @click="logout"/>
      </template>
    </BrandBar>
    <div class="choose-wedding">
      <Card class="choose-card w-full max-w-xl">
        <template #title>Wybierz wesele</template>
        <template #content>
          <div
            v-for="(item, index) in weddings"
            :key="item.id ?? index"
            class="flex items-center justify-between gap-4 py-2"
          >
            <span>{{ item.name }}</span>
            <span v-if="item.date">{{ item.date }}</span>
            <Button label="Wybierz" @click="choose(item.pivot.wedding_id)" />
          </div>
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

.choose-wedding {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 3rem 1rem;
}

</style>
