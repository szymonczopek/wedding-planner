<script setup>
import { RouterView, RouterLink } from 'vue-router'
import { useAuth } from '@/composables/useAuth.js'
import logoUrl from '@/resources/assets/wedding-planner-logo.jpg'

const { user, isAuthenticated, logout } = useAuth()
</script>

<template>
  <nav class="topnav">
    <RouterLink to="/" class="brand" aria-label="Strona główna">
      <img :src="logoUrl" alt="Wedding Planner logo" class="brand-logo" />
    </RouterLink>
    <span class="brand-name">Wedding Planner by Szymon Czopek</span>
    <div class="topnav-links">
      <template v-if="!isAuthenticated">
        <RouterLink to="/login">Logowanie</RouterLink>
        <RouterLink to="/register">Rejestracja</RouterLink>
      </template>
      <template v-else>
        <span class="topnav-user">{{ user?.email }}</span>
        <button class="topnav-logout" @click="logout">Wyloguj</button>
      </template>
    </div>
  </nav>
  <main class="content">
    <RouterView />
  </main>
</template>

<style scoped>
.topnav {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: 1rem;
  padding: 0.6rem 1.5rem;
  background: #c6a15b;
  box-shadow: 0 2px 10px rgba(160, 124, 51, 0.35);
}

.brand {
  display: inline-flex;
  align-items: center;
  line-height: 0;
  justify-self: start;
}

.brand-name {
  justify-self: center;
  font-family: var(--font-script);
  font-size: 2.5rem;
  font-weight: 400;
  letter-spacing: normal;
  color: #ffffff;
  text-shadow: 0 1px 3px rgba(122, 90, 32, 0.55);
  white-space: nowrap;
}

.brand-logo {
  height: 120px;
  width: auto;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.45);
}

.topnav-links {
  display: flex;
  align-items: center;
  justify-self: end;
  gap: 1.25rem;
}

.topnav-links a {
  color: #ffffff;
  font-weight: 700;
  letter-spacing: 0.02em;
  text-decoration: none;
  padding-bottom: 2px;
  border-bottom: 2px solid transparent;
}

.topnav-links a:hover {
  border-bottom-color: rgba(255, 255, 255, 0.7);
}

.topnav-links a.router-link-active {
  border-bottom-color: #ffffff;
}

.topnav-user {
  color: #ffffff;
  font-weight: 700;
}

.topnav-logout {
  padding: 0.45rem 1.1rem;
  font: inherit;
  font-weight: 700;
  color: #ffffff;
  background: transparent;
  border: 1.5px solid #ffffff;
  border-radius: 999px;
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease;
}

.topnav-logout:hover {
  background: #ffffff;
  color: #a07c33;
}

.content {
  padding: 2rem;
}

@media (max-width: 720px) {
  .brand-name {
    display: none;
  }
}
</style>
