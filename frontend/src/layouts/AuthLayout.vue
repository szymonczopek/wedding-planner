<script setup>
import { ref } from 'vue'
import BrandBar from '../components/BrandBar.vue'
import UserToolbar from '../components/UserToolbar.vue'

defineProps({
  spread: { type: Boolean, default: false },
})

const mobileNavOpen = ref(false)
</script>

<template>
  <div class="auth-layout">
    <BrandBar show-menu-button @toggle-nav="mobileNavOpen = true">
      <template #end>
        <UserToolbar compact />
      </template>
    </BrandBar>
    <div class="auth-page" :class="{ 'auth-page--spread': spread }">
      <slot />
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

.auth-page--spread {
  justify-content: space-around;
}

.auth-page :deep(.auth-card) {
  width: 100%;
  max-width: 480px;
}

.auth-page :deep(.auth-form) {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  margin-top: 0.5rem;
}

.auth-page :deep(.auth-switch) {
  margin: 0;
  text-align: center;
  font-size: 0.92rem;
}
</style>
