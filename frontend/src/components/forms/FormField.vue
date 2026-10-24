<script setup>
import FloatLabel from 'primevue/floatlabel'
import Message from 'primevue/message'

defineProps({
  id: { type: String, required: true },
  label: { type: String, required: true },
  error: { type: String, default: '' },
})
</script>

<template>
  <div class="field-group">
    <FloatLabel variant="on">
      <slot :id="id" :invalid="!!error" />
      <label :for="id">{{ label }}</label>
    </FloatLabel>
    <Message v-if="error" severity="error" size="small" variant="simple">
      {{ error }}
    </Message>
  </div>
</template>

<style scoped>
.field-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}
</style>
