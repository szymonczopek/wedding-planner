<script setup>
import { ref } from 'vue'
import { RouterView } from 'vue-router'
import Button from 'primevue/button'
import Avatar from 'primevue/avatar'
import Drawer from 'primevue/drawer'
import BrandBar from '../components/BrandBar.vue'
import AppMenu from '../components/AppMenu.vue'
import { useAuth } from '../composables/useAuth.js'

const { user, logout } = useAuth()
const mobileNavOpen = ref(false)
</script>

<template>
  <div class="app-layout">
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

.user-name, .user-invite-code {
  font-weight: 600;
  margin: 0 0.25rem;
  color: #000000;
}

.brandBar-user-data {
  text-align: center;
}

.logout-btn {
  background: var(--p-menu-background, var(--p-surface-0, #ffffff));
  border-color: var(--p-menu-border-color, transparent);
  color: var(--p-menu-item-color, var(--p-text-color));
}

.logout-btn:hover {
  background: var(--p-menu-item-focus-background, var(--p-surface-100, #f5f5f5));
  border-color: var(--p-menu-border-color, transparent);
  color: var(--p-menu-item-focus-color, var(--p-text-color));
}

.user-invite-code {
  color: black;
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
  //border-right: 1px solid var(--p-content-border-color);
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

  .user-name {
    display: none;
  }

  .app-content {
    padding: 1rem;
  }
}
</style>
