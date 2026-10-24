<script setup>
import {ref, computed, onBeforeUnmount, onMounted} from 'vue'
import { RouterView, useRouter } from 'vue-router'
import Button from 'primevue/button'
import Avatar from 'primevue/avatar'
import Drawer from 'primevue/drawer'
import Menu from 'primevue/menu'
import BrandBar from '../components/BrandBar.vue'
import AppMenu from '../components/AppMenu.vue'
import { useAuth } from '../composables/useAuth.js'

const router = useRouter()
const { user, logout, getUserWithAllWeddings } = useAuth()

const mobileNavOpen = ref(false)
const weddingMenuOpen = ref(false)
const allWeddings = ref([])
let closeTimer = null

const weddings = computed(() => user.value?.weddings ?? [])

const selectedWedding = computed(() =>
    weddings.value.find((w) => w.pivot?.selected) ?? weddings.value[0] ?? null
)

const roleLabel = computed(() => selectedWedding.value?.pivot?.role_label ?? '')
const weddingName = computed(() => selectedWedding.value?.name ?? '')

const weddingMenuItems = computed(() => {
  const items = [
    {
      label: 'Dołącz lub stwórz nowe',
      icon: 'pi pi-plus',
      command: () => goTo('/create-wedding'),
    },
  ]

  if (allWeddings.value.length > 1) {
    items.push({
      label: 'Przełącz',
      icon: 'pi pi-sync',
      command: () => goTo('/choose-wedding'),
    })
  }

  return items
})

function openWeddingMenu() {
  clearTimeout(closeTimer)
  weddingMenuOpen.value = true
}

function scheduleCloseWeddingMenu() {
  clearTimeout(closeTimer)
  closeTimer = setTimeout(() => {
    weddingMenuOpen.value = false
  }, 150)
}

function goTo(path) {
  weddingMenuOpen.value = false
  router.push(path)
}

onMounted(async () => {
  try {
    const data = await getUserWithAllWeddings()
    allWeddings.value = data.weddings ?? []
  } catch {
    allWeddings.value = []
  }
})

onBeforeUnmount(() => clearTimeout(closeTimer))
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
            <div
              class="wedding-name"
              @mouseenter="openWeddingMenu"
              @mouseleave="scheduleCloseWeddingMenu"
            >
              <span>{{ weddingName }}</span>
              <Menu
                v-if="weddingMenuOpen"
                :model="weddingMenuItems"
                class="wedding-menu"
              />
            </div>
            <div class="user-role">
              <label>Rola: </label>
              <span>{{ roleLabel }}</span>
            </div>
            <div class="user-invite-code">
              <label>Kod wesela: </label>
              <span>{{ selectedWedding?.invite_code }}</span>
            </div>
        </div>
        <Button label="Wyloguj" icon="pi pi-sign-out" class="black-button" @click="logout" />
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

.user-name, .user-invite-code, .user-role, .wedding-name {
  font-weight: 600;
  margin: 0.25rem 0.25rem;
  color: #000000;
  border: 1px solid transparent;
  border-radius: 4px;
}

.wedding-name {
  position: relative;
}

.wedding-name:hover {
  border-color: grey;
}

.wedding-name :deep(.wedding-menu) {
  position: absolute;
  top: 100%;
  left: 0;
  min-width: 14rem;
  z-index: 1000;
}

.brandBar-user-data {
  text-align: center;
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

.user-name {
  display: none;
}

.app-content {
  padding: 1rem;
}
}
</style>
