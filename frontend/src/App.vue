<script setup>
import { RouterView, RouterLink } from 'vue-router'
</script>

<template class="main">
  <nav style="padding:1rem; display:flex; gap:1rem; border-bottom:1px solid #eee;">
    <RouterLink to="/">Home</RouterLink>
  </nav>
  <main style="padding:2rem;">
    <RouterView />
  </main>
</template>

<style>
.main{
  background: red;
}
</style>
