<script setup>
import { computed, onMounted, ref } from 'vue'
import Button from 'primevue/button'
import Card from 'primevue/card'
import Message from 'primevue/message'
import AuthLayout from '../../layouts/AuthLayout.vue'
import { useAuth } from '../../composables/useAuth.js'
import { useWedding } from '../../composables/useWedding.js'
import { getErrorMessage } from '../../api/errors.js'

const { getUserWithAllWeddings } = useAuth()
const { chooseWedding, isChoosing } = useWedding()

const userWithAllWeddings = ref(null)
const error = ref(null)

onMounted(async () => {
  try {
    userWithAllWeddings.value = await getUserWithAllWeddings()
  } catch (e) {
    error.value = getErrorMessage(e, 'Nie udało się pobrać listy wesel')
  }
})

const weddings = computed(() => userWithAllWeddings.value?.weddings ?? [])

async function choose(id) {
  error.value = null
  try {
    await chooseWedding(id)
  } catch (e) {
    error.value = getErrorMessage(e, 'Nie udało się wybrać wesela')
  }
}
</script>

<template>
  <AuthLayout>
    <Card class="w-full max-w-xl">
      <template #title>Wybierz wesele</template>
      <template #content>
        <Message v-if="error" severity="error">{{ error }}</Message>
        <div
          v-for="(item, index) in weddings"
          :key="item.id ?? index"
          class="flex items-center justify-between gap-4 py-2"
        >
          <span>{{ item.name }}</span>
          <span v-if="item.date">{{ item.date }}</span>
          <Button label="Wybierz" :loading="isChoosing" @click="choose(item.pivot.wedding_id)" />
        </div>
      </template>
    </Card>
  </AuthLayout>
</template>
