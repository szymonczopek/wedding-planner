<script setup>
import {computed, onMounted, ref} from 'vue'
import Button from 'primevue/button'
import AuthLayout from '../layouts/AuthLayout.vue'
import {useAuth} from '../composables/useAuth.js'
import {useWedding} from '../composables/useWedding.js'
import Card from "primevue/card";

const {getUserWithAllWeddings} = useAuth()
const {chooseWedding} = useWedding()

const name = ref('')
const invite_code = ref('')
const date = ref(null)
const userWithAllWeddings = ref(null)
const chooseErrors = ref({})

onMounted(async () => {
  userWithAllWeddings.value = await getUserWithAllWeddings()
})

const weddings = computed(() => userWithAllWeddings.value?.weddings ?? [])

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
  <AuthLayout>
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
  </AuthLayout>
</template>

<style scoped>
.choose-wedding {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 3rem 1rem;
}

</style>
