<script setup>
import {computed, onMounted, ref} from 'vue'
import Button from 'primevue/button'
import BrandBar from '../components/BrandBar.vue'
import {useAuth} from '../composables/useAuth.js'
import {useWedding} from '../composables/useWedding.js'
import Card from "primevue/card";

const {logout, getUserWithWeddings} = useAuth()
const {chooseWedding} = useWedding()

const mobileNavOpen = ref(false)
const name = ref('')
const invite_code = ref('')
const date = ref(null)
const userWithWeddings = ref(null)
const chooseErrors = ref({})

onMounted(async () => {
  userWithWeddings.value = await getUserWithWeddings()
})

const weddings = computed(() => userWithWeddings.value?.weddings ?? [])

console.log(weddings)
async function choose(id) {
  chooseErrors.value = {}
  try {
    await chooseWedding(id)
  } catch (e) {
    chooseErrors.value = e.response?.data?.errors ?? {}
  }
}

</script>

<template>
  <div class="auth-layout">
    <BrandBar show-menu-button @toggle-nav="mobileNavOpen = true">
      <template #end>
        <Button label="Wyloguj" icon="pi pi-sign-out" class="logout-btn" @click="logout"/>
      </template>
    </BrandBar>
    <div class="choose-wedding">
      <Card class="choose-card">
        <template #title>Wybierz wesele</template>
        <template #content>
          <p v-for="(item, index) in weddings" :key="item.id ?? index">
              {{ item.name }}
              <Button label="Wybierz" @click="choose(item.pivot.wedding_id)" />
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

.choose-wedding {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-around;
  padding: 3rem 1rem;
}

</style>
