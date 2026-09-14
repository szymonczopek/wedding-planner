<script setup>
import Toolbar from 'primevue/toolbar'
import Button from 'primevue/button'
import logoUrl from '@/resources/assets/wedding-planner-logo.jpg'

defineProps({
  showMenuButton: { type: Boolean, default: false },
})

defineEmits(['toggle-nav'])
</script>

<template>
  <Toolbar class="brand-bar">
    <template #start>
      <Button
        v-if="showMenuButton"
        icon="pi pi-bars"
        severity="secondary"
        text
        class="nav-toggle"
        aria-label="Otwórz nawigację"
        @click="$emit('toggle-nav')"
      />
      <img :src="logoUrl" alt="Wedding Planner logo" class="brand-logo" />
    </template>
    <template #center>
      <span class="brand-name">Wedding Planner by Szymon Czopek</span>
    </template>
    <template #end>
      <slot name="end" />
    </template>
  </Toolbar>
</template>

<style scoped>
.brand-bar {
  border-radius: 0;
  border-left: none;
  border-right: none;
  border-top: none;
  height: 130px;
  background: var(--gold);
}

.brand-logo {
  height: 6.5rem;
  width: auto;
  border-radius: 6px;
}

.brand-name {
  font-weight: 500;
  font-size: 2rem;
  white-space: nowrap;
  text-align: center;
  color: black;
}

.nav-toggle {
  display: none;
}

@media (max-width: 860px) {
  .nav-toggle {
    display: inline-flex;
  }

  .brand-name {
    display: none;
  }
}
</style>
