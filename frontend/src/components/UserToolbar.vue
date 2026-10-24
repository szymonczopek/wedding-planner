<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import Avatar from 'primevue/avatar'
import Button from 'primevue/button'
import Menu from 'primevue/menu'
import { useAuth } from '../composables/useAuth.js'

const props = defineProps({
  compact: { type: Boolean, default: false },
})

const router = useRouter()
const { user, logout, getUserWithAllWeddings } = useAuth()

const weddingMenuOpen = ref(false)
const allWeddings = ref([])
let closeTimer = null

const weddings = computed(() => user.value?.weddings ?? [])
const selectedWedding = computed(
  () => weddings.value.find((w) => w.pivot?.selected) ?? weddings.value[0] ?? null,
)
const roleLabel = computed(() => selectedWedding.value?.pivot?.role_label ?? '')
const weddingName = computed(() => selectedWedding.value?.name ?? '')
const hasSelectedWedding = computed(() => weddings.value.length > 0)

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
  if (props.compact) return

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
  <template v-if="compact">
    <Button
      v-if="hasSelectedWedding"
      label="Pulpit"
      icon="pi pi-home"
      class="black-button mr-5"
      @click="router.push('/')"
    />
    <Button v-if="user" label="Wyloguj" icon="pi pi-sign-out" class="black-button" @click="logout" />
  </template>
  <template v-else>
    <div class="brandBar-user-data">
      <div>
        <Avatar
          :label="user?.name?.[0]?.toUpperCase() ?? '?'"
          shape="circle"
          class="user-avatar"
        />
        <span class="user-name">{{ user?.role_label }} {{ user?.name }}</span>
      </div>
      <div
        class="wedding-name"
        @mouseenter="openWeddingMenu"
        @mouseleave="scheduleCloseWeddingMenu"
      >
        <span>{{ weddingName }}</span>
        <Menu v-if="weddingMenuOpen" :model="weddingMenuItems" class="wedding-menu" />
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
</template>

<style scoped>
.user-name,
.user-invite-code,
.user-role,
.wedding-name {
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

@media (max-width: 860px) {
  .user-name {
    display: none;
  }
}
</style>
