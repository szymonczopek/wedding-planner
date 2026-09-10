<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import Menu from 'primevue/menu'

const items = ref([
  { label: 'Pulpit', icon: 'pi pi-home', route: '/' },
  { label: 'Lista gości', icon: 'pi pi-users', route: '/guests' },
  { label: 'Stoły', icon: 'pi-th-large', route: '/tables' },
])
</script>

<template>
  <Menu :model="items" class="app-menu">
    <template #item="{ item, props }">
      <RouterLink v-if="item.route" :to="item.route" v-bind="props.action" class="menu-router-link">
        <span :class="item.icon" />
        <span>{{ item.label }}</span>
      </RouterLink>
      <a v-else v-bind="props.action" class="menu-router-link">
        <span :class="item.icon" />
        <span>{{ item.label }}</span>
      </a>
    </template>
  </Menu>
</template>

<style scoped>
.app-menu {
  width: 100%;
  border: none;
}

.menu-router-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
  color: inherit;
}

.menu-router-link.router-link-active {
  font-weight: 700;
}
</style>
