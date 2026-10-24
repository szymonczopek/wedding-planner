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
          class="black-button mr-5"
          @click="router.push('/')"
        />
        <Button label="Wyloguj" icon="pi pi-sign-out" class="black-button" @click="logout" />
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

.black-button {
  background: var(--p-menu-background, var(--p-surface-0, #ffffff));
  border-color: var(--p-menu-border-color, transparent);
  color: var(--p-menu-item-color, var(--p-text-color));
}

.black-button:hover {
  background: var(--p-menu-item-focus-background, var(--p-surface-100, #f5f5f5));
  border-color: var(--p-menu-border-color, transparent);
  color: var(--p-menu-item-focus-color, var(--p-text-color));
}
</style>
