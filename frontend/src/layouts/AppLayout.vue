<script setup>
import { ref } from 'vue'
import { RouterView } from 'vue-router'
import Drawer from 'primevue/drawer'
import BrandBar from '../components/BrandBar.vue'
import AppMenu from '../components/AppMenu.vue'
import UserToolbar from '../components/UserToolbar.vue'

const mobileNavOpen = ref(false)
</script>

<template>
  <div class="app-layout">
    <BrandBar show-menu-button @toggle-nav="mobileNavOpen = true">
      <template #end>
        <UserToolbar />
      </template>
    </BrandBar>

    <div class="app-body">
      <aside class="app-sidebar">
        <AppMenu />
      </aside>
      <main class="app-content">
        <RouterView />
      </main>
    </div>

    <Drawer v-model:visible="mobileNavOpen" header="Nawigacja" position="left" class="mobile-drawer">
      <div @click="mobileNavOpen = false">
        <AppMenu />
      </div>
    </Drawer>
  </div>
</template>

<style scoped>
.app-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.app-body {
  flex: 1;
  display: flex;
  min-height: 0;
}

.app-sidebar {
  width: 16rem;
  flex-shrink: 0;
  padding: 1rem 0.75rem;
}

.app-content {
  flex: 1;
  padding: 2rem;
  min-width: 0;
}

.mobile-drawer {
  width: 18rem;
}

@media (max-width: 860px) {
  .app-sidebar {
    display: none;
  }

  .app-content {
    padding: 1rem;
  }
}
</style>
