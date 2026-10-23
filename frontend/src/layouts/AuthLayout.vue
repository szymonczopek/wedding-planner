<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import Button from 'primevue/button'
import BrandBar from '../components/BrandBar.vue'
import { useAuth } from '../composables/useAuth.js'

const router = useRouter()
const { user, logout } = useAuth()

const mobileNavOpen = ref(false)
const hasSelectedWedding = computed(() => (user.value?.weddings?.length ?? 0) > 0)
</script>

<template>
  <div class="auth-layout">
    <BrandBar show-menu-button @toggle-nav="mobileNavOpen = true">
      <template #end>
        <Button
          v-if="hasSelectedWedding"
          label="Pulpit"
          icon="pi pi-home"
          class="btn"
          @click="router.push('/')"
        />
        <Button label="Wyloguj" icon="pi pi-sign-out" class="logout-btn" @click="logout" />
      </template>
    </BrandBar>
    <slot />
  </div>
</template>

<style scoped>
.auth-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
</style>
