<script setup>
import {ref} from 'vue'
import Button from 'primevue/button'
import AddGuestForm from '../../components/forms/AddGuestForm.vue'

const isModalOpen = ref(false)
</script>

<template>
  <div>
    <div class="flex justify-end">
      <Button label="Dodaj gościa" icon="" class="black-button" @click="isModalOpen = true"/>
    </div>

    <AddGuestForm v-model:visible="isModalOpen"/>

    <div class="table">
    </div>
  </div>
</template>
