<script setup>
import {computed, reactive, ref} from 'vue'
import Button from 'primevue/button'
import Checkbox from 'primevue/checkbox'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import RadioButton from 'primevue/radiobutton'
import Select from 'primevue/select'
import FormField from '../../components/forms/FormField.vue'
import api from '../../api/client.js'
import {getValidationErrors} from '../../api/errors.js'
import {useAuth} from '../../composables/useAuth.js'

const {user} = useAuth()

const acquaintanceTypes = [
  {label: 'Świadek', value: 'witness'},
  {label: 'Rodzina bliska', value: 'close_family'},
  {label: 'Rodzina daleka', value: 'distant_family'},
  {label: 'Znajomy bliski', value: 'close_friend'},
  {label: 'Znajomy daleki', value: 'distant_friend'},
  {label: 'Sąsiad', value: 'neighbor'},
  {label: 'Osoba towarzysząca', value: 'plus_one'},
]

const diets = [
  {label: 'Standardowa', value: 'standard'},
  {label: 'Wegetariańska', value: 'vegetarian'},
  {label: 'Wegańska', value: 'vegan'},
  {label: 'Keto', value: 'keto'},
  {label: 'Lekkostrawna', value: 'light'},
  {label: 'Bezglutenowa', value: 'gluten_free'},
  {label: 'Bez laktozy', value: 'lactose_free'},
  {label: 'Bez orzechów', value: 'nut_free'},
  {label: 'Bez ryb i owoców morza', value: 'fish_and_seafood_free'},
  {label: 'Halal', value: 'halal'},
  {label: 'Koszerna', value: 'kosher'},
  {label: 'Menu dziecięce', value: 'kids_menu'},
]

const isModalOpen = ref(false)
const isSubmitting = ref(false)
const errors = ref({})
const addressMode = ref('existing')
const addressSearch = ref('')
const addressResults = ref([])
const selectedAddressId = ref(null)
const guests = ref([])

const selectedWedding = computed(() => {
  const weddings = user.value?.weddings ?? []

  return weddings.find((wedding) => wedding.pivot?.selected) ?? weddings[0] ?? null
})

const weddingId = computed(() => selectedWedding.value?.pivot?.wedding_id ?? null)

const isGroom = computed(() => selectedWedding.value?.pivot?.role === 'groom')

function emptyForm() {
  return {
    name: '',
    surname: '',
    acquaintance_type: null,
    child: false,
    groom_guest: isGroom.value,
    diet: null,
    main_guest_id: null,
    address: {
      street: '',
      house_number: '',
      local_number: '',
      city: '',
      postal_code: '',
      country: 'Polska',
    },
  }
}

const form = reactive(emptyForm())

const guestOptions = computed(() =>
    guests.value.map((guest) => ({
      label: `${guest.name} ${guest.surname}`,
      value: guest.id,
    })),
)

function resetForm() {
  Object.assign(form, emptyForm())
  errors.value = {}
  isSubmitting.value = false
  addressMode.value = 'existing'
  addressSearch.value = ''
  addressResults.value = []
  selectedAddressId.value = null
}

function searchAddress() {
  selectedAddressId.value = null
  addressResults.value = []
  // TODO: wyszukiwarka
}

async function loadGuests() {
  if (!weddingId.value) {
    guests.value = []
    return
  }

  try {
    const {data} = await api.get('/guests', {params: {wedding_id: weddingId.value}})
    guests.value = data ?? []
  } catch {
    guests.value = []
  }
}

async function addGuestModal() {
  resetForm()
  isModalOpen.value = true
  await loadGuests()
}

async function submitGuest() {
  errors.value = {}

  if (!weddingId.value) {
    errors.value = {wedding_id: ['Brak wybranego wesela']}
    return
  }

  if (addressMode.value === 'existing' && !selectedAddressId.value) {
    errors.value = {address_id: ['Wybierz adres z wyszukiwarki']}
    return
  }

  isSubmitting.value = true

  try {
    const payload = {
      name: form.name,
      surname: form.surname,
      acquaintance_type: form.acquaintance_type,
      child: form.child,
      groom_guest: form.groom_guest,
      diet: form.diet,
      wedding_id: weddingId.value,
    }

    if (form.main_guest_id) {
      payload.main_guest_id = form.main_guest_id
    }

    if (addressMode.value === 'existing') {
      payload.address_id = selectedAddressId.value
    } else {
      payload.address = {
        ...form.address,
        local_number: form.address.local_number || null,
      }
    }

    await api.post('/guests/create', payload)
    isModalOpen.value = false
    await loadGuests()
  } catch (e) {
    errors.value = getValidationErrors(e)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div>
    <div>
      <div class="flex justify-end">
        <Button label="Dodaj gościa" icon="" class="black-button" @click="addGuestModal"/>
      </div>

      <Dialog
          v-model:visible="isModalOpen"
          header="Dodaj gościa"
          modal
          :draggable="true"
          :style="{ width: '42rem' }"
          :breakpoints="{ '640px': '95vw' }"
      >
        <form id="add-guest-form" class="guest-form" @submit.prevent="submitGuest">
          1. Dane
          <div class="form-grid">
            <FormField id="guest-name" label="Imię" :error="errors.name?.[0]">
              <template #default="{ id, invalid }">
                <InputText :id="id" v-model="form.name" type="text" :invalid="invalid" fluid/>
              </template>
            </FormField>
            <FormField id="guest-surname" label="Nazwisko" :error="errors.surname?.[0]">
              <template #default="{ id, invalid }">
                <InputText :id="id" v-model="form.surname" type="text" :invalid="invalid" fluid/>
              </template>
            </FormField>
            <FormField id="guest-acquaintance-type" label="Rodzaj znajomości" :error="errors.acquaintance_type?.[0]">
              <template #default="{ id, invalid }">
                <Select
                    :inputId="id"
                    v-model="form.acquaintance_type"
                    :options="acquaintanceTypes"
                    optionLabel="label"
                    optionValue="value"
                    :invalid="invalid"
                    fluid
                />
              </template>
            </FormField>
            <FormField id="guest-diet" label="Dieta" :error="errors.diet?.[0]">
              <template #default="{ id, invalid }">
                <Select
                    :inputId="id"
                    v-model="form.diet"
                    :options="diets"
                    optionLabel="label"
                    optionValue="value"
                    :invalid="invalid"
                    fluid
                />
              </template>
            </FormField>
            <div>
              2. Strona:
              <div class="checkbox-item">
                <RadioButton
                    v-model="form.groom_guest"
                    inputId="guest-groom-side"
                    name="groom-guest-side"
                    :value="true"
                />
                <label for="guest-groom-side">Gość Pana Młodego</label>
              </div>
              <div class="checkbox-item">
                <RadioButton
                    v-model="form.groom_guest"
                    inputId="guest-bride-side"
                    name="groom-guest-side"
                    :value="false"
                />
                <label for="guest-bride-side">Gość Panny Młodej</label>
              </div>
              <div class="checkbox-item">
                <RadioButton
                    v-model="form.groom_guest"
                    inputId="guest-both"
                    name="groom-guest-side"
                    :value="null"
                />
                <label for="guest-bride-side">Gość wspólny</label>
              </div>
              3. Wiek
              <div class="checkbox-item">
                <Checkbox v-model="form.child" inputId="guest-child" binary/>
                <label for="guest-child">Dziecko</label>
              </div>
            </div>
            <div class="full-width">
              <FormField id="guest-main-guest" label="Główny gość (opcjonalnie)" :error="errors.main_guest_id?.[0]">
                <template #default="{ id, invalid }">
                  <Select
                      :inputId="id"
                      v-model="form.main_guest_id"
                      :options="guestOptions"
                      optionLabel="label"
                      optionValue="value"
                      filter
                      showClear
                      :invalid="invalid"
                      fluid
                  />
                </template>
              </FormField>
            </div>
            <div class="address-mode">
              4. Adres:
              <div class="address-mode-option">
                <RadioButton
                    v-model="addressMode"
                    inputId="address-mode-existing"
                    name="address-mode"
                    value="existing"
                />
                <label for="address-mode-existing">Dołącz do adresu zapisanego gościa</label>
              </div>
              <div class="address-mode-option">
                <RadioButton
                    v-model="addressMode"
                    inputId="address-mode-new"
                    name="address-mode"
                    value="new"
                />
                <label for="address-mode-new">Dodaj nowy adres</label>
              </div>
            </div>

            <div v-if="addressMode === 'existing'" class="form-grid">
              <div class="full-width">
                <FormField id="address-search" label="Wyszukiwarka adresu" :error="errors.address_id?.[0]">
                  <template #default="{ id, invalid }">
                    <InputText
                        :id="id"
                        v-model="addressSearch"
                        type="text"
                        :invalid="invalid"
                        fluid
                        @input="searchAddress"
                    />
                  </template>
                </FormField>
                <Select
                    v-if="addressResults.length"
                    v-model="selectedAddressId"
                    :options="addressResults"
                    optionLabel="label"
                    optionValue="id"
                    placeholder="Wybierz adres"
                    class="address-results"
                    fluid
                />
                <small v-else class="hint">Wyszukiwarka adresu zostanie podłączona później.</small>
              </div>
            </div>

            <div v-else class="form-grid">
              <FormField id="address-street" label="Ulica" :error="errors['address.street']?.[0]">
                <template #default="{ id, invalid }">
                  <InputText :id="id" v-model="form.address.street" type="text" :invalid="invalid" fluid/>
                </template>
              </FormField>
              <FormField id="address-house-number" label="Numer domu" :error="errors['address.house_number']?.[0]">
                <template #default="{ id, invalid }">
                  <InputText :id="id" v-model="form.address.house_number" type="text" :invalid="invalid" fluid/>
                </template>
              </FormField>
              <FormField id="address-local-number" label="Numer lokalu" :error="errors['address.local_number']?.[0]">
                <template #default="{ id, invalid }">
                  <InputText :id="id" v-model="form.address.local_number" type="text" :invalid="invalid" fluid/>
                </template>
              </FormField>
              <FormField id="address-city" label="Miasto" :error="errors['address.city']?.[0]">
                <template #default="{ id, invalid }">
                  <InputText :id="id" v-model="form.address.city" type="text" :invalid="invalid" fluid/>
                </template>
              </FormField>
              <FormField id="address-postal-code" label="Kod pocztowy" :error="errors['address.postal_code']?.[0]">
                <template #default="{ id, invalid }">
                  <InputText :id="id" v-model="form.address.postal_code" type="text" :invalid="invalid" fluid/>
                </template>
              </FormField>
              <FormField id="address-country" label="Kraj" :error="errors['address.country']?.[0]">
                <template #default="{ id, invalid }">
                  <InputText :id="id" v-model="form.address.country" type="text" :invalid="invalid" fluid/>
                </template>
              </FormField>
            </div>
          </div>

          <div class="modal-actions">
            <Button type="button" label="Anuluj" severity="secondary" text @click="isModalOpen = false"/>
            <Button type="submit" label="Dodaj" :loading="isSubmitting"/>
          </div>
        </form>
      </Dialog>
    </div>
    <div class="table">
    </div>
  </div>
</template>

<style scoped>
.guest-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.full-width {
  grid-column: 1 / -1;
}

.address-mode {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.address-mode-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0.5rem 0;
}


.address-results {
  margin-top: 0.5rem;
}

.hint {
  display: block;
  margin-top: 0.5rem;
  color: var(--p-text-muted-color, #6b7280);
  font-size: 0.85rem;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

@media (max-width: 640px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
